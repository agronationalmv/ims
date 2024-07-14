<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
            'email' => 'it@example.com',
        ]);

        Role::create(['name'=>'Super Admin']);

        \App\Models\User::first()->assignRole('Super Admin');

        \App\Models\Unit::create([
            'name'=>'kg'
        ]);
        \App\Models\Unit::create([
            'name'=>'pcs'
        ]);


        \App\Models\Supplier::create([
            'name'=>'Madihaa Pvt. Ltd.',
            'gst_tin_no'=>'GST09TIN03944',
            'address'=>'SOme where, Male'
        ]);

        \App\Models\AdjustmentType::create(['name'=>'Damage']);

        \App\Models\Store::create(['name'=>'Gaadhoo Food Main']);

    }
}
