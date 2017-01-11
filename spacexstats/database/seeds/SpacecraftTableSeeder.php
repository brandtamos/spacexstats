<?php
use Illuminate\Database\Seeder;
use SpaceXStats\Models\Spacecraft;

class SpacecraftTableSeeder extends Seeder {
    public function run() {
        // COTS1
        Spacecraft::create([
            'name' => 'Dragon C1',
            'type' => 'Dragon 1'
        ]);

        // COTS2+
        Spacecraft::create([
            'name' => 'Dragon C2',
            'type' => 'Dragon 1'
        ]);

        // CRS-1
        Spacecraft::create([
            'name' => 'Dragon C3',
            'type' => 'Dragon 1'
        ]);

        // CRS-2
        Spacecraft::create([
            'name' => 'Dragon C4',
            'type' => 'Dragon 1'
        ]);

        // CRS-3
        Spacecraft::create([
            'name' => 'Dragon C5',
            'type' => 'Dragon 1.1'
        ]);

        Spacecraft::create([
            'name' => 'Dragon C6',
            'type' => 'Dragon 1.1'
        ]);

        Spacecraft::create([
            'name' => 'Dragon C7',
            'type' => 'Dragon 1.1'
        ]);

        Spacecraft::create([
            'name' => 'Dragon C8',
            'type' => 'Dragon 1.1'
        ]);

        Spacecraft::create([
            'name' => 'Dragon C9',
            'type' => 'Dragon 1.1'
        ]);

        // CRS-8
        Spacecraft::create([
            'name' => 'Dragon C10',
            'type' => 'Dragon 1.1'
        ]);
    }
}