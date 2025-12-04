<?php

namespace Database\Seeders;

use App\Models\Seat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // sectors
        $sectors = ['A', 'B', 'C'];
        $seatsInSector = 50;
        foreach ($sectors as $sector) {
            for ($i = 1; $i <= $seatsInSector; $i++) {
                $seatNumber = $sector . str_pad($i, 3, '0', STR_PAD_LEFT);

                Seat::create([
                    'seat_number' => $seatNumber,
                    'base_price' => 5000 + (ord('C') - ord($sector)) * 1000,
                ]);
            }
        }

        // premium sector
        $seatsInPremium = 5;
        for ($i = 1; $i <= $seatsInPremium; $i++) {
            Seat::create([
                'seat_number' => 'P' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'base_price' => 10000,
            ]);
        }
    }
}
