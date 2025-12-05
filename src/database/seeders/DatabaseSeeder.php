<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProviderSeeder::class, // <— nom exactement comme ton fichier/classe
        ]);
    }
}
