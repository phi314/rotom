<?php

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            [
                'name' => 'Cuci + Setrika',
                'unit' => 'kg',
                'price' => '6000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Cuci',
                'unit' => 'kg',
                'price' => '5000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Setrika',
                'unit' => 'kg',
                'price' => '5000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Bed Cover',
                'unit' => 'pcs',
                'price' => '20000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Selimut',
                'unit' => 'pcs',
                'price' => '17000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Boneka',
                'unit' => 'pcs',
                'price' => '15000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Bantal',
                'unit' => 'pcs',
                'price' => '15000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Gordain',
                'unit' => 'pcs',
                'price' => '10000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Tas',
                'unit' => 'pcs',
                'price' => '15000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Karpet',
                'unit' => 'm',
                'price' => '20000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Sepatu',
                'unit' => 'pcs',
                'price' => '20000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Sleeping Bag',
                'unit' => 'pcs',
                'price' => '20000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Gaun',
                'unit' => 'pcs',
                'price' => '15000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Keset Kaki',
                'unit' => 'pcs',
                'price' => '10000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Kemeja',
                'unit' => 'pcs',
                'price' => '10000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Celana',
                'unit' => 'pcs',
                'price' => '10000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Jaket',
                'unit' => 'pcs',
                'price' => '25000',
                'admin_user_id' => 1
            ],
            [
                'name' => 'Jas',
                'unit' => 'pcs',
                'price' => '15000',
                'admin_user_id' => 1
            ]
        ]);
    }
}
