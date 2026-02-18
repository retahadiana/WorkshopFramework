<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Ensure test user exists and has a bcrypt password
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Seed categories
        $novel = Category::firstOrCreate(['name' => 'Novel']);
        $bio = Category::firstOrCreate(['name' => 'Biografi']);
        $komik = Category::firstOrCreate(['name' => 'Komik']);

        // Seed books per assignment
        Book::firstOrCreate([
            'code' => 'NV-01'
        ],[
            'category_id' => $novel->id,
            'title' => 'Home Sweet Loan',
            'author' => 'Almira Bastari'
        ]);

        Book::firstOrCreate([
            'code' => 'BO-01'
        ],[
            'category_id' => $bio->id,
            'title' => 'Mohammad Hatta, Untuk Negeriku',
            'author' => 'Taufik Abdullah'
        ]);

        Book::firstOrCreate([
            'code' => 'NV-02'
        ],[
            'category_id' => $novel->id,
            'title' => 'Keajaiban Toko Kelontong Namiya',
            'author' => 'Keigo Higashino'
        ]);
    }
}
