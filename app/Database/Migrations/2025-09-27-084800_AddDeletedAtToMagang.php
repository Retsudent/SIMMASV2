<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToMagang extends Migration
{
    public function up()
    {
        $this->forge->addColumn('magang', [
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('magang', 'deleted_at');
    }
}
