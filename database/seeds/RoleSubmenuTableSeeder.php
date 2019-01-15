<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSubmenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_submenu')->insert([
            'role_id' => 1,
            'submenu_id' => 1
        ]);

        DB::table('role_submenu')->insert([
            'role_id' => 1,
            'submenu_id' => 2
        ]);

        DB::table('role_submenu')->insert([
            'role_id' => 1,
            'submenu_id' => 3
        ]);
    }
}
