<?php

use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = ['Chair', 'Car', 'Computer', 'Gloves', 'Pants', 'Shirt', 'Table', 'Shoes', 'Hat', 'Plate', 'Knife', 'Bottle', 'Coat', 'Lamp', 'Keyboard', 'Bag', 'Bench', 'Clock', 'Watch', 'Wallet'];

        foreach ($items as $key => $value) {
            $items = DB::table('m_item')->insert([
                'name'              => $value,
                'location'          => 'Gudang 1',
                'unit'              => 'pcs'
            ]);
        }
    }
}
