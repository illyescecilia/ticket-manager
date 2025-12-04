<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseTicketRequest;
use Illuminate\Support\Facades\Auth;

class TicketPurchaseController extends Controller
{
    /**
     * Show seats.
     */
    public function create(Event $event)
    {
        $allSeats = Seat::orderBy('seat_number')->get();
        $takenSeatIds = Ticket::where('event_id', $event->id)->pluck('seat_id')->toArray();
        $userTicketCount = Ticket::where('event_id', $event->id)
                                 ->where('user_id', Auth::id())
                                 ->count();
        $remainingUserLimit = $event->max_number_allowed - $userTicketCount;

        $totalSeatsCount = $allSeats->count();
        $takenSeatsCount = count($takenSeatIds);
        $occupancy = ($totalSeatsCount > 0) ? ($takenSeatsCount / $totalSeatsCount) : 0;
        $daysUntil = max(0, now()->startOfDay()->diffInDays($event->event_date_at->startOfDay()));

        return view('events.purchase', [
            'event' => $event,
            'allSeats' => $allSeats,
            'takenSeatIds' => $takenSeatIds,
            'remainingUserLimit' => $remainingUserLimit,
            'occupancy' => $occupancy,
            'daysUntil' => $daysUntil,
        ]);
    }

    /**
     * Process purchase.
     */
    public function store(PurchaseTicketRequest $request, Event $event)
    {
        $selectedSeatIds = $request->validated('seats');
        $user = Auth::user();

        $allSeats = Seat::whereIn('id', $selectedSeatIds)->get()->keyBy('id');
        $totalSeatsCount = Seat::count();
        $takenSeatsCount = Ticket::where('event_id', $event->id)->count();
        $occupancy = ($totalSeatsCount > 0) ? (($takenSeatsCount) / $totalSeatsCount) : 0;
        $daysUntil = max(0, now()->startOfDay()->diffInDays($event->event_date_at->startOfDay()));

        DB::transaction(function () use ($selectedSeatIds, $allSeats, $event, $user, $occupancy, $daysUntil) {

            foreach ($selectedSeatIds as $seatId) {
                $seat = $allSeats[$seatId];
                $price = $seat->base_price;

                if ($event->is_dynamic_price) {
                    $price = $seat->base_price * (1 - 0.5 * (1 / ($daysUntil + 1))) * (1 + 0.5 * $occupancy);
                    $price = round($price);
                }

                do {
                    $barcode = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
                } while (Ticket::where('barcode', $barcode)->exists());

                Ticket::create([
                    'barcode' => $barcode,
                    'admission_time' => null,
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'seat_id' => $seat->id,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('tickets.my-tickets')->with('success', 'A jegyvásárlás sikeres!');
    }
}
