<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $operationalTypes = [
            ['name' => 'Tagihan Bulanan GPS', 'is_custom' => 0],
            ['name' => 'Cicilan', 'is_custom' => 0],
            ['name' => 'Filter Solar', 'is_custom' => 0],
            ['name' => 'Ban atau Velg', 'is_custom' => 0],
            ['name' => 'Bomper', 'is_custom' => 0],
            ['name' => 'Tambahan Ongkos Jalan', 'is_custom' => 0],
            ['name' => 'Tambahan Gaji Supir 2', 'is_custom' => 0],
            ['name' => 'Gomok', 'is_custom' => 0],
            ['name' => 'Tali Tambang', 'is_custom' => 0],
            ['name' => 'Ban Dalam', 'is_custom' => 0],
            ['name' => 'Las', 'is_custom' => 0],
            ['name' => 'Servis AC', 'is_custom' => 0],
            ['name' => 'Oli & Sparepart Rem', 'is_custom' => 0],
            ['name' => 'Kir', 'is_custom' => 0],
            ['name' => 'Gaji Sopir', 'is_custom' => 0],
            ['name' => 'BPJS Kesehatan', 'is_custom' => 0],
            ['name' => 'BPJS Ketenagakerjaan', 'is_custom' => 0],
            ['name' => 'Biaya Minum', 'is_custom' => 0],
            ['name' => 'Uang Makan Sopir', 'is_custom' => 0],
            ['name' => 'Per', 'is_custom' => 0],
            ['name' => 'tambahan uang jalan', 'is_custom' => 0],
        ];

        // Tambahkan created_at ke setiap item
        foreach ($operationalTypes as &$type) {
            $type['created_at'] = now();
        }

        DB::table('operational_types')->insert($operationalTypes);
    }
}