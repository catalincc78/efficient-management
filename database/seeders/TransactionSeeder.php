<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $userId = 1;
        foreach (range(1, 40) as $index) {
            $id = DB::table('transactions')->insertGetId(['user_id' => $userId]);
            foreach (range(1, 10) as $index) {
                $amountSign = (rand() % 2 === 0) ? 1 : -1;
                $target_type =  rand(0,1) > 0.5 ? 'product' : 'activity';
                $amount = rand(100,1000) * $amountSign;
                if($target_type === 'product') {
                    $product_id = Products::all()->random(1)->first();
                    $quantity = rand(0,20) * (-1 * $amountSign);
                    $activity = null;
                }else{
                    $activity = $faker->phrase();
                    $product_id = null;
                    $quantity = null;
                }
                DB::table('transacted_items')->insert([
                    'transaction_id' => $id,
                    'target_type' => $target_type,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'activity' => $activity,
                    'amount' => $amount
                ]);
            }
        }
    }
}
