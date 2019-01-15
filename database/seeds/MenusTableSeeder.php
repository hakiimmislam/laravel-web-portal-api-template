<?php

use Illuminate\Database\Seeder;
use App\Menu;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::create([
            'id' => 1,
            'name' => 'Menu Management',
            'slug' => 'menu-management',
            'icon' => 'menu-unfold',
            'order' => 1
        ]);

        Menu::create([
            'id' => 2,
            'name' => 'User Management',
            'slug' => 'user-management',
            'icon' => 'team',
            'order' => 2
        ]);
    }
}
