<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display future events.
     */
    public function index()
    {
        $now = date('Y-m-d H:i:s');

        $futureEvents = Event::where('event_date_at', '>', $now)
            ->orderBy('event_date_at', 'asc')
            ->paginate(5);

        $totalSeats = Seat::count();

        $soldTickets = Ticket::select('event_id', DB::raw('count(*) as sold_count'))
            ->groupBy('event_id')
            ->get()
            ->keyBy('event_id');

        return view('events.index', [
            'events' => $futureEvents,
            'totalSeats' => $totalSeats,
            'soldTickets' => $soldTickets
        ]);
    }

    /**
     * Show the details of an event.
     */
    public function show(Event $event)
    {
        return view('events.show', ['event' => $event]);
    }
}
