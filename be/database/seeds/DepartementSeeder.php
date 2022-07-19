<?php

use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departement = ['calendar', 'logistik'];

        foreach ($departement as $key => $value) {
            DB::table('m_departement')->insert([
                'name'              => $value
            ]);
        }
    }
}
