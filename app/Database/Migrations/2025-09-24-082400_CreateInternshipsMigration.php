<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInternshipsMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('magang', true);
        $this->forge->addField([
            'id' => [
                'type' => 'SERIAL',
                'null' => false,
            ],
            'siswa_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'dudi_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'guru_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'status' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending' ],
            'nilai_akhir' => [ 'type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => true,
            ],
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
        $this->forge->addKey(['siswa_id', 'guru_id', 'dudi_id']);
        $this->forge->createTable('magang');
    }

    public function down()
    {
        $this->forge->dropTable('magang');
    }
}
