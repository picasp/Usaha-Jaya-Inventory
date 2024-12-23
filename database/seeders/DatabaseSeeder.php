<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\TransaksiMasuk;
use App\Models\TransaksiKeluar;
use App\Models\TransaksiKeluarItem;
use App\Models\Opname;
use App\Models\TransaksiMasukItem;
use App\Models\OpnameItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin123'),
        ]);
        Barang::factory(50)->create();
        Supplier::factory(10)->create();
        TransaksiMasuk::factory(20)->create();
        TransaksiKeluar::factory(30)->create();
        TransaksiKeluarItem::factory(100)->create();
        Opname::factory(10)->create();
        TransaksiMasukItem::factory(50)->create();
        OpnameItem::factory(30)->create();
    }
}
