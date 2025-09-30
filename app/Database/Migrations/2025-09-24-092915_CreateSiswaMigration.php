<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('siswa', true);
        $this->forge->addField([
            'id' => ['type' => 'SERIAL', 'null' => false],
            'user_id' => ['type' => 'INT', 'null' => false],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nis' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kelas' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jurusan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'telepon' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa');
    }
}
