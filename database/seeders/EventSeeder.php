<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'title' => 'Vizsgaidőszak',
            'description' => 'A vizsgaidőszak egy tragikus időszak, ami minden félévben terrorizálja a szerencsétlen diákokat.',
            'sale_start_at' => fake()->dateTimeBetween('-3 weeks', '-1 week'),
            'sale_end_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'event_date_at' => fake()->dateTimeBetween('now', '+1 week'),
            'is_dynamic_price' => fake()->boolean(25),
            'max_number_allowed' => fake()->numberBetween(1, 5),
            'cover_image' => 'events/finals_week.jpg',
        ]);

        Event::create([
            'title' => 'Holnap három határidő',
            'description' => 'A diáklét egyik feledhetetlen élménye, amikor hirtelen minden tárgyból is adnak valamilyen beadandót, ráadásul hasonló határidőkkel.',
            'sale_start_at' => fake()->dateTimeBetween('-3 weeks', '-1 week'),
            'sale_end_at' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
            'event_date_at' => fake()->dateTimeBetween('+2 weeks', '+3 week'),
            'is_dynamic_price' => fake()->boolean(25),
            'max_number_allowed' => fake()->numberBetween(1, 5),
            'cover_image' => 'events/three-deadlines-tomorrow.jpg',
        ]);

        for ($i=0; $i < fake()->numberBetween(5, 15); $i++) {
            $sale_start = fake()->dateTimeBetween('-2 weeks', '+2 weeks');
            $sale_end = fake()->dateTimeBetween($sale_start, '+1 month');
            $event_date = fake()->dateTimeBetween($sale_end, '+1 month');

            Event::create([
                'title' => fake()->sentence(3),
                'description' => fake()->text(300),
                'sale_start_at' => $sale_start,
                'sale_end_at' => $sale_end,
                'event_date_at' => $event_date,
                'is_dynamic_price' => fake()->boolean(25),
                'max_number_allowed' => fake()->numberBetween(1, 5),
                'cover_image' => fake()->imageUrl(640, 480, 'concert', true),
            ]);
        }
    }
}
