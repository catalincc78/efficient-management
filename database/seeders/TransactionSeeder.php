<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_US');
        $faker->locale('en_US');
        $userId = 1;
        $startDate = Carbon::create(2023, 8, 1);
        $endDate = Carbon::create(2023, 8, 30);

        $companyActivities = [
            'Organize Book Sale Event',
            'Host Author Meetup',
            'Conduct Storytelling Workshop',
            'Prepare Monthly Newsletter',
            'Library Renovation',
            'Staff Training',
            'Marketing Campaign',
            'Online Book Recommendation Service',
            'Book Cataloging',
            'Create Reading Lists',
            'Collaborate with Local Schools',
            'Community Book Swap',
            'Create Reading Challenges',
            'Library App Development',
            'Promote E-books Collection',
            'Feature Local Artists',
            'Literary Trivia Night',
            'Book Club Meetings',
            'Support Literacy Programs',
            'Book Signing Event',
            'Themed Book Displays',
            'Library Fundraising',
            'Children’s Storytime Session',
            'Digital Resources Promotion',
            'Collaborate with Authors',
            'Library Book Drive',
            'Interactive Workshops',
            'Book Cover Design Contest',
            'Library Outreach Program',
            'Library Membership Drive',
            'Library Website Updates',
            'Host Poetry Slam',
            'Science Fiction Symposium',
            'Host Academic Seminars',
            'Library Social Media Campaign',
            'Collaborate with Local Bookstores',
            'Reader’s Advisory Service',
            'Collaborate with Community Centers',
            'Diversity in Literature Campaign',
            'Teen Book Club',
            'Adult Coloring Sessions',
            'Author Q&A Sessions',
            'Multilingual Storytelling Hours',
            'Technology Workshops',
            'Historical Fiction Book Talks',
            'Literary Movie Nights',
            'Local History Exhibitions',
            'Book Swap Events',
            'Creative Writing Workshops',
            'Environmental Book Talks',
            'Bookbinding Workshops',
            'Library Book Sale',
            'Mystery Book Discussion Groups',
            'Book-themed Art Exhibitions',
            'Bookish Podcast Creation',
            'Interactive Reading Challenges',
            'Family Reading Time',
            'Library Book Recommendations Podcast',
            'Library Escape Room Challenge',
        ];

        foreach (range(1, 80) as $index) {
            $createdAt = $faker->dateTimeBetween($startDate, $endDate);
            $id = DB::table('transactions')->insertGetId(['user_id' => $userId, 'created_at' => $createdAt, 'updated_at' => $createdAt]);
            foreach (range(1, 6) as $index) {
                $amountSign = (rand() % 2 === 0) ? 1 : -1;
                $target_type =  rand(0,1) > 0.5 ? 'product' : 'activity';
                $amount = ($amountSign > 0 ? rand(3000, 7000) : rand(1000, 5000))* $amountSign;
                if($target_type === 'product') {
                    $product_id = Products::all()->random(1)->first()->id;
                    $quantity = rand(0,30) * (-1 * $amountSign);
                    $activity = null;
                }else{
                    $activity = $faker->randomElement($companyActivities);
                    $product_id = null;
                    $quantity = 0;
                }
                DB::table('transacted_items')->insert([
                    'transaction_id' => $id,
                    'target_type' => $target_type,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'activity' => $activity,
                    'amount' => $amount,
                ]);
            }
        }
    }
}
