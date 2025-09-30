<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLogoUrlColumn extends Migration
{
    public function up()
    {
        // Change logo_url column from VARCHAR(500) to TEXT
        $this->forge->modifyColumn('school_settings', [
            'logo_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Revert back to VARCHAR(500)
        $this->forge->modifyColumn('school_settings', [
            'logo_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
        ]);
    }
}


