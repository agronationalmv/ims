<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com'
        ]);

        \App\Models\Unit::create([
            'name'=>'kg'
        ]);
        \App\Models\Unit::create([
            'name'=>'pcs'
        ]);

        \App\Models\Product::create([
            'name'=>'Carrot',
            'unit_id'=>1,
            'min_qty'=>5,
            'qty'=>10,
            'price'=>50,
            'gst_rate'=>0
        ]);

        \App\Models\Product::create([
            'name'=>'Apple',
            'unit_id'=>1,
            'min_qty'=>15,
            'qty'=>10,
            'price'=>5,
            'gst_rate'=>0.08
        ]);


        \App\Models\Product::create([
            'name'=>'Fresh Eggs',
            'unit_id'=>2,
            'min_qty'=>10,
            'qty'=>24,
            'price'=>1.5,
            'gst_rate'=>0
        ]);


        \App\Models\Supplier::create([
            'name'=>'Madihaa Pvt. Ltd.',
            'gst_tin_no'=>'GST09TIN03944',
            'address'=>'SOme where, Male'
        ]);

    }
}
