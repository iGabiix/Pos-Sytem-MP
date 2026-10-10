<?php

namespace Tests;

use App\Libraries\Money;
use App\Libraries\SaleService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use DomainException;

final class SaleServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = 'App';
    protected $refresh = true;
    protected $migrateOnce = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db->table('users')->insert(['id' => 1, 'username' => 'test_staff', 'full_name' => 'Test Staff', 'password' => password_hash('A-Test-Password-2026', PASSWORD_DEFAULT), 'created_at' => date('Y-m-d H:i:s')]);
        $this->db->table('customers')->insert(['id' => 1, 'full_name' => 'Test Customer', 'email' => 'test@example.com', 'created_at' => date('Y-m-d H:i:s')]);
        $this->db->table('products')->insert(['id' => 1, 'name' => 'Test Product', 'price' => '19.99', 'stock_quantity' => 5, 'created_at' => date('Y-m-d H:i:s')]);
    }

    private function record(int $quantity = 2, ?int $customer = 1, string $key = ''): int
    {
        return (new SaleService($this->db))->record(1, $customer, 1, $quantity, $key ?: bin2hex(random_bytes(32)));
    }

    private function stock(): int
    {
        return (int) $this->db->table('products')->where('id', 1)->get()->getRow('stock_quantity');
    }

    public function testSalePersistsExactTotalAndDecrementsStock(): void
    {
        $id = $this->record();
        $sale = $this->db->table('sales')->where('id', $id)->get()->getRowArray();
        $this->assertSame(3, $this->stock());
        $this->assertSame(3998, Money::cents((string) $sale['total_price']));
        $this->assertSame(1, (int) $sale['customer_id']);
        $this->assertSame(1, (int) $sale['sold_by']);
    }

    public function testWalkInSaleCanExhaustStockExactly(): void
    {
        $id = $this->record(5, null);
        $this->assertSame(0, $this->stock());
        $this->assertNull($this->db->table('sales')->where('id', $id)->get()->getRow('customer_id'));
    }

    public function testOversellingChangesNeitherStockNorSales(): void
    {
        try {
            $this->record(6);
            $this->fail('Overselling should be rejected.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('Only 5', $e->getMessage());
        }
        $this->assertSame(5, $this->stock());
        $this->assertSame(0, $this->db->table('sales')->countAllResults());
    }

    public function testDuplicateSubmissionDoesNotChargeStockTwice(): void
    {
        $key = bin2hex(random_bytes(32));
        $first = $this->record(2, 1, $key);
        $second = $this->record(2, 1, $key);
        $this->assertSame($first, $second);
        $this->assertSame(3, $this->stock());
        $this->assertSame(1, $this->db->table('sales')->countAllResults());
    }

    public function testArchivedProductCannotBeSold(): void
    {
        $this->db->table('products')->where('id', 1)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        try {
            $this->record();
            $this->fail('Archived product should be rejected.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('no longer available', $e->getMessage());
        }
        $this->assertSame(5, $this->stock());
    }

    public function testDeletedCustomerIsRejected(): void
    {
        $this->db->table('customers')->where('id', 1)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('customer is no longer available');
        $this->record();
    }

    public function testInvalidStaffIsRejected(): void
    {
        $this->expectException(DomainException::class);
        (new SaleService($this->db))->record(1, null, 99, 1, bin2hex(random_bytes(32)));
    }

    public function testInvalidQuantityIsRejected(): void
    {
        $this->expectException(DomainException::class);
        $this->record(0);
    }

    public function testMoneyOverflowRollsBackStock(): void
    {
        $this->db->table('products')->where('id', 1)->update(['price' => '99999999.99']);
        try {
            $this->record(2);
            $this->fail('Overflow should be rejected.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('maximum', $e->getMessage());
        }
        $this->assertSame(5, $this->stock());
        $this->assertSame(0, $this->db->table('sales')->countAllResults());
    }

    public function testSaleHistorySurvivesArchivingRelatedRecords(): void
    {
        $id = $this->record();
        foreach (['products', 'customers', 'users'] as $table) {
            $this->db->table($table)->where('id', 1)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        }
        $history = (new \App\Models\SaleModel($this->db))->history()->find($id);
        $this->assertSame('Test Product', $history['product_name']);
        $this->assertSame('Test Customer', $history['customer_name']);
        $this->assertSame('Test Staff', $history['staff_name']);
    }

    public function testDatabaseFailureRollsBackInventory(): void
    {
        // Force an INSERT failure after the stock UPDATE.
        $this->db->query($this->db->DBDriver === 'MySQLi'
            ? "CREATE TRIGGER reject_test_sale BEFORE INSERT ON sales FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'test insert failure'"
            : "CREATE TRIGGER reject_test_sale BEFORE INSERT ON sales BEGIN SELECT RAISE(ABORT, 'test insert failure'); END");
        try {
            $this->record();
            $this->fail('The database error should bubble up.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            $this->assertStringContainsString('test insert failure', $e->getMessage());
        }
        $this->assertSame(5, $this->stock());
        $this->assertSame(0, $this->db->table('sales')->countAllResults());
    }
}

