<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartementSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ItemsSeeder::class);
        $this->call(StockSeeder::class);
    }
}
