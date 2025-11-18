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

        DB::table('job_batches')->truncate();

        DB::table('job_batches')->insert([
            
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}