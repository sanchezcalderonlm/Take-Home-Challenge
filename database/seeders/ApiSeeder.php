<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('apis')->insert([
            ['id' => 1, 'name' => 'newsapi'],
            ['id' => 2, 'name' => 'nytimes'],
            ['id' => 3, 'name' => 'opennews'],
        ]);
    }
}
