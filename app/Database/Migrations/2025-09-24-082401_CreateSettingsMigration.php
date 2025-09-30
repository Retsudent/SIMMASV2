<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('school_settings', true);
        $this->forge->addField([
            'id' => [
                'type' => 'SERIAL',
                'null' => false,
            ],
            'logo_url' => [ 'type' => 'TEXT', 'null' => true ],
            'nama_sekolah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'telepon' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'null' => true ],
            'email' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'website' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'kepala_sekolah' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'npsn' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'null' => true ],
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
        $this->forge->createTable('school_settings');
    }

    public function down()
    {
        $this->forge->dropTable('school_settings');
    }
}
