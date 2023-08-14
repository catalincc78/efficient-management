<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_US');
        $faker->locale('en_US');
        $userId = 1;
        foreach (range(1, 40) as $index) {
            DB::table('products')->insert([
                'user_id' => $userId,
                'name' => $faker->unique()->word,
                'sku' => Helper::generateSKU()
            ]);
        }
    }
}
