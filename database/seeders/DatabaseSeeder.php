<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookType;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $countries = Country::factory()->count(10)->create();
        City::factory()->count(50)->create();
        Address::factory()->count(50)->create();
        Language::factory()->count(10)->create();

        $countries->each(function (Country $country) {
            $randomLanguages = Language::inRandomOrder()->take(random_int(1, 3))->get()->unique();
            $country->languages()->attach($randomLanguages);
        });

        Publisher::factory()->count(5)->create();
        collect(['hardcover', 'paperback', 'e-book'])
            ->each(fn(string $bookType) => BookType::create(['name' => $bookType]));
        $books = Book::factory()->count(250)->create();

        Genre::factory()->count(7)->create();
        Author::factory()->count(150)->create();

        collect(['paid', 'pending', 'rejected'])
            ->each(fn(string $orderStatus) => OrderStatus::create(['name' => $orderStatus]));

        Customer::factory()->count(300)->create();
        $orders = Order::factory()->count(1000)->create();

        $books->each(function (Book $book) {
            $randomGenres = Genre::inRandomOrder()->take(random_int(1, 3))->get()->unique();
            $randomAuthors = Author::inRandomOrder()->take(random_int(1, 3))->get()->unique();

            $book->genres()->attach($randomGenres);
            $book->authors()->attach($randomAuthors);
        });

        $orders->each(function (Order $order) {
            $randomBooks = Book::inRandomOrder()->take(random_int(1, 5))->get()->unique();
            $attach = [];
            foreach ($randomBooks as $book) {
                $attach[$book->id] = [
                    'quantity' => random_int(1, 2),
                    'price' => $book->price,
                ];
            }

            $order->books()->attach($attach);
        });
    }
}
