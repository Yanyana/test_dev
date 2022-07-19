<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        
        for ($i=0; $i < 10; $i++) {
            $name = $faker->name;
            $user = 'username'.$i;
            $nik = $faker->nik();

            DB::table('users')->insert([
                'name'              => $name,
                'email'             => strtolower(explode(" ",$name)[0].'@email.com'),
                'username'          => $user,
                'nik'               => $nik,
                'departement_id'    => rand(1,2),
                'password'          => Hash::make('123456')
    		]);
        }

        $passports = [
            [NULL,	'Laravel Personal Access Client',	'$2y$10$hcummN2OWvvzK41.0vEAWeyOufQb26rNRQKXl6UssXIFtFN3Y3SsS',	NULL,	'http://localhost',	1,	0,	0,	'2022-07-18 20:53:30',	'2022-07-18 20:53:30'],
            [NULL,	'Laravel Password Grant Client',	'$2y$10$nc.LmzFcL99V/ci3Nqu/feWFgB9s6UMOvMRHQA9V5INDKBzVqn8Ky',	'users',	'http://localhost',	0,	1,	0,	'2022-07-18 20:53:30',	'2022-07-18 20:53:30']
        ];
        
        foreach ($passports as $key => $value) {
            DB::table('oauth_clients')->insert([
                'user_id'       => $value[0],
                'name'          => $value[1],
                'secret'        => $value[2],
                'provider'      => $value[3],
                'redirect'      => $value[4],
                'personal_access_client'=> $value[5],
                'password_client'=> $value[6],
                'revoked'       => $value[7],
                'created_at'    => $value[8],
                'updated_at'    => $value[9],
            ]);
        }
    }
}
