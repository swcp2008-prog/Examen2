<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedCacheLocksSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('cache_locks')->insertOrIgnore([
            
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}