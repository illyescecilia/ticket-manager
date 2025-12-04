<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $events = Event::all();
        $seats = Seat::all();
        $totalSeatsCount = $seats->count();

        if ($users->isEmpty() || $events->isEmpty() || $seats->isEmpty()) {
            $this->command->info("Can't seed tickets: Missing Users, Events, or Seats.");
            return;
        }

        $availableSeatsByEvent = [];
        $userTicketsPerEvent = [];

        foreach ($events as $event) {
            $availableSeatsByEvent[$event->id] = $seats->keyBy('id');
        }

        foreach ($users as $user) {
            $ticketsToTryToBuy = rand(1, 5);

            for ($i = 0; $i < $ticketsToTryToBuy; $i++) {
                $availableEvents = $events->filter(function ($event) use ($availableSeatsByEvent, $userTicketsPerEvent, $user) {
                    $eventId = $event->id;

                    // filter out events where there are no seats left
                    if (empty($availableSeatsByEvent[$eventId]) || $availableSeatsByEvent[$eventId]->isEmpty()) {
                        return false;
                    }

                    // filter out events where the user reacher the ticket limit
                    $currentCount = $userTicketsPerEvent[$user->id][$eventId] ?? 0;
                    if ($currentCount >= $event->max_number_allowed) {
                        return false;
                    }

                    return true;
                });

                // break if there are no events with available seats left
                if ($availableEvents->isEmpty()) {
                    break;
                }

                // choose a random event and seat

                $event = $availableEvents->random();
                $eventId = $event->id;

                $seat = $availableSeatsByEvent[$eventId]->random();
                $seatId = $seat->id;

                // generating the remaining fillables

                $finalPrice = $seat->base_price;
                if ($event->is_dynamic_price) {
                    $randomFactor = fake()->numberBetween(1, 10);
                    switch ($randomFactor) {
                        case 1: $finalPrice = round($seat->base_price * 0.80, 0); break;
                        case 2: case 3: $finalPrice = round($seat->base_price * 0.90, 0); break;
                        case 8: case 9: $finalPrice = round($seat->base_price * 1.10, 0); break;
                        case 10: $finalPrice = round($seat->base_price * 1.20, 0); break;
                        default: break;
                    }
                }

                $purchaseTime = fake()->dateTimeBetween($event->sale_start_at, $event->sale_end_at);

                do {
                    $barcode = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
                } while (Ticket::where('barcode', $barcode)->exists());

                // create ticket
                Ticket::create([
                    'barcode' => $barcode,
                    'admission_time' => null,
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'seat_id' => $seatId,
                    'price' => $finalPrice,
                    'created_at' => $purchaseTime,
                    'updated_at' => $purchaseTime,
                ]);

                $availableSeatsByEvent[$eventId]->forget($seatId);

                if (!isset($userTicketsPerEvent[$user->id])) {
                    $userTicketsPerEvent[$user->id] = [];
                }
                $userTicketsPerEvent[$user->id][$eventId] = ($userTicketsPerEvent[$user->id][$eventId] ?? 0) + 1;
            }
        }
    }
}
