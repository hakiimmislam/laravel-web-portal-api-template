<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu_role')->insert([
            'role_id' => 1,
            'menu_id' => 1
        ]);

        DB::table('menu_role')->insert([
            'role_id' => 1,
            'menu_id' => 2
        ]);
    }
}
