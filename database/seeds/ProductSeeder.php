<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        for ($i = 0; $i < 30; $i++) {
            App\Product::create([
                'name' => $faker->productName,
                'desc' => $faker->text,
                'img' => 'https://assets.swappie.com/iPhone-X-64GB-Silver-1-1-1-1.png',
                'price' => rand(12 * 10, 1000 * 10) / 10,
                'weight' => rand(10 * 10, 100 * 10) / 10,
            ]);
        }
    }
}
