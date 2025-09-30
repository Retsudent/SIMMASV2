<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanupSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        
        echo "Cleaning up duplicate data...\n";
        
        // Delete all magang data
        $db->table('magang')->truncate();
        echo "Deleted all magang data\n";
        
        // Delete all logbook data
        $db->table('logbook')->truncate();
        echo "Deleted all logbook data\n";
        
        // Delete all siswa data
        $db->table('siswa')->truncate();
        echo "Deleted all siswa data\n";
        
        // Delete all guru data
        $db->table('guru')->truncate();
        echo "Deleted all guru data\n";
        
        // Delete all users except admin
        $db->table('users')->where('role !=', 'admin')->delete();
        echo "Deleted all non-admin users\n";
        
        echo "Cleanup completed!\n";
    }
}
