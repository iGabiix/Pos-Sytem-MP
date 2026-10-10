<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use DomainException;
use Throwable;

final class SaleService
{
    public function __construct(private BaseConnection $db)
    {
    }

    public function record(int $productId, ?int $customerId, int $staffId, int $quantity, string $requestKey): int
    {
        if ($quantity < 1 || $quantity > 1000000 || !preg_match('/^[a-f0-9]{64}$/D', $requestKey)) {
            throw new DomainException('Enter a whole quantity between 1 and 1,000,000 and submit a fresh sale form.');
        }
        $this->db->transException(true)->transBegin();
        try {
            $existing = $this->db->table('sales')->where('request_key', $requestKey)->get()->getRowArray();
            if ($existing) {
                if ((int) $existing['sold_by'] !== $staffId) {
                    throw new DomainException('Please open a new sale form.');
                }
                $this->db->transCommit();
                return (int) $existing['id'];
            }

            $lock = $this->db->DBDriver === 'MySQLi' ? ' FOR UPDATE' : '';
            if (!$this->db->query('SELECT id FROM users WHERE id = ? AND deleted_at IS NULL' . $lock, [$staffId])->getRowArray()) {
                throw new DomainException('Your staff account is no longer active. Please sign in again.');
            }
            if ($customerId !== null && !$this->db->query('SELECT id FROM customers WHERE id = ? AND deleted_at IS NULL' . $lock, [$customerId])->getRowArray()) {
                throw new DomainException('That customer is no longer available. Choose another customer or Walk-in.');
            }

            // A guarded UPDATE is atomic. Competing sales cannot take stock below zero.
            $this->db->query(
                'UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND deleted_at IS NULL AND stock_quantity >= ?',
                [$quantity, $productId, $quantity]
            );
            if ($this->db->affectedRows() !== 1) {
                $product = $this->db->table('products')->where('id', $productId)->where('deleted_at', null)->get()->getRowArray();
                throw new DomainException($product
                    ? 'Insufficient stock. Only ' . $product['stock_quantity'] . ' unit(s) of ' . $product['name'] . ' are available.'
                    : 'That product is no longer available. Please select another product.');
            }
            // The UPDATE holds a write lock until commit; price cannot change beneath this read.
            $product = $this->db->table('products')->where('id', $productId)->get()->getRowArray();
            $total = Money::cents((string) $product['price']) * $quantity;
            if ($total > Money::MAX_CENTS) {
                throw new DomainException('This sale exceeds the maximum transaction total. Reduce the quantity.');
            }
            $this->db->table('sales')->insert([
                'product_id' => $productId, 'customer_id' => $customerId, 'sold_by' => $staffId,
                'quantity' => $quantity, 'total_price' => Money::decimal($total),
                'request_key' => $requestKey, 'created_at' => date('Y-m-d H:i:s'),
            ]);
            $id = (int) $this->db->insertID();
            if (!$this->db->transStatus()) {
                throw new \RuntimeException('The sale transaction failed.');
            }
            $this->db->transCommit();
            return $id;
        } catch (Throwable $e) {
            $this->db->transRollback();
            // A simultaneous retry can hit the unique key after waiting for the first commit.
            $existing = $this->db->table('sales')->where('request_key', $requestKey)->where('sold_by', $staffId)->get()->getRowArray();
            if ($existing) {
                return (int) $existing['id'];
            }
            throw $e;
        }
    }
}

