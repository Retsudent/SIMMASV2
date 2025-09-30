<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersMigration extends Migration
{
    public function up()
    {
        $this->forge->dropTable('users', true);
        $this->forge->addField([
            'id' => [ 'type' => 'SERIAL', 'null' => false ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 255 ],
            'email' => [ 'type' => 'VARCHAR', 'constraint' => 255 ],
            'email_verified_at' => [ 'type' => 'TIMESTAMP', 'null' => true ],
            'password' => [ 'type' => 'VARCHAR', 'constraint' => 255 ],
            'role' => [ 'type' => 'VARCHAR', 'constraint' => 20 ],
            'created_at' => [ 'type' => 'TIMESTAMP', 'null' => true ],
            'updated_at' => [ 'type' => 'TIMESTAMP', 'null' => true ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        // Emulate enum for role
        $this->db->simpleQuery("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin','siswa','guru','dudi'))");
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
