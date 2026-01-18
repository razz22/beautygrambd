<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couriers = [
            [
                'title' => 'Pathao',
                'logo' => 'assets/back-end/img/courier/pathao.svg',
                'type' => 'Fixed',
                'min_charge' => 0,
                'max_charge' => 0,
                'delivery_charge' => 60.00,
                'base_url' => 'https://api-hermes.pathao.com',
                'test_base_url' => 'https://hermes-api.p-stageenv.xyz',
                'client_id' => null,
                'client_secret' => null,
                'test_client_id' => null,
                'test_client_secret' => null,
                'client_email' => null,
                'client_password' => null,
                'grant_type' => 'password',
                'store_id' => null,
                'is_live' => false,
                'is_active' => true,
            ],
            [
                'title' => 'SteadFast',
                'logo' => 'assets/back-end/img/courier/steadFast.svg',
                'type' => 'Fixed',
                'min_charge' => 0,
                'max_charge' => 0,
                'delivery_charge' => 60.00,
                'base_url' => null,
                'test_base_url' => null,
                'client_id' => null,
                'client_secret' => null,
                'test_client_id' => null,
                'test_client_secret' => null,
                'client_email' => null,
                'client_password' => null,
                'grant_type' => 'password',
                'store_id' => null,
                'is_live' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Redx',
                'logo' => 'assets/back-end/img/courier/redx.svg',
                'type' => 'Fixed',
                'min_charge' => 0,
                'max_charge' => 0,
                'delivery_charge' => 60.00,
                'base_url' => null,
                'test_base_url' => null,
                'client_id' => null,
                'client_secret' => null,
                'test_client_id' => null,
                'test_client_secret' => null,
                'client_email' => null,
                'client_password' => null,
                'grant_type' => 'password',
                'store_id' => null,
                'is_live' => false,
                'is_active' => true,
            ],
        ];

        foreach ($couriers as $courier) {
            Courier::updateOrCreate(
                ['title' => $courier['title']],
                $courier
            );
        }
    }
}
