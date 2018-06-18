<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'name'  => 'The Godfather',
            'price' => '59.99'
        ]);

        DB::table('products')->insert([
            'name'  => 'Steve Jobs',
            'price' => '49.95'
        ]);

        DB::table('products')->insert([
            'name'  => 'The Return of Sherlock Holmes',
            'price' => '39.99'
        ]);

        DB::table('products')->insert([
            'name'  => 'The Little Prince',
            'price' => '29.99'
        ]);

        DB::table('products')->insert([
            'name'  => 'I Hate Myselfie!',
            'price' => '19.99'
        ]);

        DB::table('products')->insert([
            'name'  => 'The Trial',
            'price' => '9.99'
        ]);
    }
}
