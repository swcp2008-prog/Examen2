<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedJobBatchesSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('job_batches')->insertOrIgnore([
            
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}