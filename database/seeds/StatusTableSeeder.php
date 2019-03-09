<?php

use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('status')->delete();

        \DB::table('status')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Rechazado',
                    'created_at' => '2019-03-09 13:10:39',
                    'updated_at' => '2019-03-09 13:10:39',
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'En tramite',
                    'created_at' => '2019-03-09 13:10:39',
                    'updated_at' => '2019-03-09 13:10:39',
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'Aceptado',
                    'created_at' => '2019-03-09 13:10:39',
                    'updated_at' => '2019-03-09 13:10:39',
                ),
        ));
    }
}
