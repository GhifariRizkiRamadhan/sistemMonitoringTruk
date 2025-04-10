<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cargoTypes = [
            ['name' => 'Batu Bara AJC', 'is_custom' => 0],
            ['name' => 'Klinker', 'is_custom' => 0],
            ['name' => 'Limbah SBE', 'is_custom' => 0],
            ['name' => 'Batu Bara KBB', 'is_custom' => 0],
            ['name' => 'Cangkang', 'is_custom' => 0],
            ['name' => 'Pozolan', 'is_custom' => 0],
            ['name' => 'Semen Sak', 'is_custom' => 0],
            ['name' => 'Pupuk', 'is_custom' => 0],
            ['name' => 'Besi', 'is_custom' => 0],
            ['name' => 'Clay', 'is_custom' => 0],
            ['name' => 'Limbah Slut GPAL', 'is_custom' => 0],
            ['name' => 'Batu Bara Teluk Bayur', 'is_custom' => 0],
            ['name' => 'flyash', 'is_custom' => 0],
            ['name' => 'Bottomash', 'is_custom' => 0],
            ['name' => 'gypsum', 'is_custom' => 0],
            ['name' => 'Semen JumboBag', 'is_custom' => 0],
            ['name' => 'Batu Bara Dumai', 'is_custom' => 0],
        ];

        // Tambahkan created_at ke setiap item
        foreach ($cargoTypes as &$type) {
            $type['created_at'] = now();
        }

        DB::table('cargo_types')->insert($cargoTypes);
    }
}