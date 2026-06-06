<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;

class UpdateSalesProductNameSeeder extends Seeder
{
    public function run(): void
    {
        $sales = Sale::with('product')->get();
        
        foreach ($sales as $sale) {
            if ($sale->product) {
                $sale->update(['product_name' => $sale->product->product_name]);
            } else {
                $sale->update(['product_name' => 'Deleted Product']);
            }
        }
    }
}