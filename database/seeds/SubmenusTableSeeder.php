<?php

use Illuminate\Database\Seeder;
use App\Submenu;

class SubmenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Submenu::create([
        	'menu_id' => 2,
            'name' => 'Roles',
            'slug' => 'roles',
        	'icon' => 'crown',
        	'order' => 1
        ]);

        Submenu::create([
        	'menu_id' => 2,
            'name' => 'Permissions',
            'slug' => 'permissions',
        	'icon' => 'file-protect',
        	'order' => 2
        ]);

        Submenu::create([
        	'menu_id' => 2,
            'name' => 'Users',
            'slug' => 'users',
        	'icon' => 'user',
        	'order' => 3
        ]);
    }
}
