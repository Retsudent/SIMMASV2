<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupDuplicates extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:cleanup-duplicates';
    protected $description = 'Clean up duplicate internship placements';

    public function run(array $params)
    {
        $db = db_connect();

        CLI::write('Cleaning up duplicate placements for Ahmad Rizki (siswa_id=3)...', 'yellow');

        // Delete logbook entries for duplicate magang records
        $logbookDeleted = $db->table('logbook')->whereIn('magang_id', [1, 2, 4])->delete();
        CLI::write("Deleted logbook entries: $logbookDeleted", 'green');

        // Delete duplicate magang records (keep only the latest one - ID 5)
        $magangDeleted = $db->table('magang')->whereIn('id', [1, 2, 4])->delete();
        CLI::write("Deleted magang entries: $magangDeleted", 'green');

        // Show remaining data
        CLI::write("\nRemaining magang data:", 'blue');
        $remaining = $db->table('magang m')
            ->select('m.id, m.siswa_id, u.name as siswa_nama, m.guru_id, g.nama as guru_nama, m.status')
            ->join('users u', 'u.id = m.siswa_id', 'left')
            ->join('guru g', 'g.id = m.guru_id', 'left')
            ->get()->getResultArray();

        foreach ($remaining as $row) {
            CLI::write("ID: {$row['id']}, Siswa: {$row['siswa_nama']}, Guru: {$row['guru_nama']}, Status: {$row['status']}");
        }

        CLI::write("\nDone!", 'green');
    }
}
