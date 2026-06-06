<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => '2.7kg LPG', 'description' => 'Small LPG tank for household use'],
            ['category_name' => '5kg LPG', 'description' => 'Medium LPG tank'],
            ['category_name' => '11kg LPG', 'description' => 'Standard household LPG tank'],
            ['category_name' => '22kg LPG', 'description' => 'Large LPG tank for commercial use'],
            ['category_name' => '50kg LPG', 'description' => 'Industrial LPG tank'],
            ['category_name' => 'Accessories', 'description' => 'Regulators, hoses, and other accessories'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}