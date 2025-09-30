<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDudisMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('dudi', true);
        $this->forge->addField([
            'id' => [
                'type' => 'SERIAL',
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'nama_perusahaan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'penanggung_jawab' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'default' => 'aktif' ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('dudi');
    }

    public function down()
    {
        $this->forge->dropTable('dudi');
    }
}
