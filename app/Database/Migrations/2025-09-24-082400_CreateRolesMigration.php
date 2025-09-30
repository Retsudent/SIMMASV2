<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesMigration extends Migration
{
    public function up()
    {
        // ERD tidak menggunakan tabel roles terpisah -> skip pembuatan tabel ini
    }

    public function down()
    {
        // nothing
    }
}
