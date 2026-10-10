<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class DemoSeeder extends Seeder
{
    public function run()
    {
        if (ENVIRONMENT !== 'development' && ENVIRONMENT !== 'testing') {
            throw new RuntimeException('Demo data is available only in development or testing.');
        }
        foreach (['products', 'customers', 'users', 'sales'] as $table) {
            if ($this->db->table($table)->countAllResults() > 0) {
                throw new RuntimeException('Demo seeding requires empty tables. Existing store data has not been changed.');
            }
        }
        $this->db->transException(true)->transStart();
        foreach (DemoData::rows() as $table => $rows) {
            $this->db->table($table)->insertBatch($rows);
        }
        $this->db->transComplete();
    }
}

