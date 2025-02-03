<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        // Truncate the students table
        DB::table('students')->truncate();

        $faker = Faker::create();

        $students = [];

        for ($i = 0; $i < 30; $i++) {
            $students[] = [
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                // 'created_at' => now(),
                // 'updated_at' => now(),
            ];
        }

        DB::table('students')->insert($students);
    }
}