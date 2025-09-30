<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJournalsMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('logbook', true);
        $this->forge->addField([
            'id' => [
                'type' => 'SERIAL',
                'null' => false,
            ],
            'magang_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'kegiatan' => [
                'type' => 'TEXT',
            ],
            'kendala' => [ 'type' => 'TEXT', 'null' => true ],
            'file' => [ 'type' => 'VARCHAR', 'constraint' => 500, 'null' => true ],
            'status_verifikasi' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pending',
            ],
            'catatan_guru' => [ 'type' => 'TEXT', 'null' => true ],
            'catatan_dudi' => [ 'type' => 'TEXT', 'null' => true ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [ 'type' => 'TIMESTAMP', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['magang_id', 'tanggal']);
        $this->forge->createTable('logbook');
    }

    public function down()
    {
        $this->forge->dropTable('logbook');
    }
}
