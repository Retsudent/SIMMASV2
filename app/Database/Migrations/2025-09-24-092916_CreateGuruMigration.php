<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGuruMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('guru', true);
        $this->forge->addField([
            'id' => ['type' => 'SERIAL', 'null' => false],
            'user_id' => ['type' => 'INT', 'null' => false],
            'nip' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 255],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'telepon' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('guru');
    }

    public function down()
    {
        $this->forge->dropTable('guru');
    }
}
