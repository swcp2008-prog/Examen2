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

        DB::table('cache_locks')->truncate();

        DB::table('cache_locks')->insert([
            
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}