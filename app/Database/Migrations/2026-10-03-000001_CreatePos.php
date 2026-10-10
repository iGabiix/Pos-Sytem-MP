<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePos extends Migration
{
    public function up()
    {
        $id = ['type' => 'INT', 'auto_increment' => true];
        $created = ['type' => 'DATETIME'];
        $deleted = ['type' => 'DATETIME', 'null' => true];
        $options = $this->db->DBDriver === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];

        $this->forge->addField([
            'id' => $id, 'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'stock_quantity' => ['type' => 'INT', 'default' => 0],
            'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => $created, 'deleted_at' => $deleted,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products', true, $options);

        $this->forge->addField([
            'id' => $id, 'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => $created, 'deleted_at' => $deleted,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers', true, $options);

        $this->forge->addField([
            'id' => $id, 'username' => ['type' => 'VARCHAR', 'constraint' => 50],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => $created, 'deleted_at' => $deleted,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users', true, $options);

        $this->forge->addField([
            'id' => $id, 'product_id' => ['type' => 'INT'],
            'customer_id' => ['type' => 'INT', 'null' => true],
            'sold_by' => ['type' => 'INT'], 'quantity' => ['type' => 'INT'],
            'total_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'request_key' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'created_at' => $created,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('request_key');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('sold_by', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('sales', true, $options);
    }

    public function down()
    {
        foreach (['sales', 'users', 'customers', 'products'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}

