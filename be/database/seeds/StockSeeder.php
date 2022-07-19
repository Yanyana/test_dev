<?php

use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = DB::select('select id from m_item order by id');
        
        foreach ($items as $key => $value) {
            DB::table('t_stock_item')->insert([
                'item_id'           => $value->id,
                'qty'               => rand(10, 300)
            ]);
        }
    }
}
