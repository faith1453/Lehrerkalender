<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run() {
        DB::table('teachers')
            ->insert(
                [
                    'username' => 'admin',
                    'email' => 'admin@gso-koeln.de',
                    'password' => bcrypt('password'),
                    'is_admin' => true
                ]
            );
    }
}