<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedPasswordResetTokensSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('password_reset_tokens')->truncate();

        DB::table('password_reset_tokens')->insert([
            
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}