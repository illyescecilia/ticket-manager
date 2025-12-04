<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTicketController extends Controller
{
    /**
     * Show the tickets of the user.
     */
    public function index()
    {
        $tickets = Auth::user()
                    ->tickets()
                    ->with(['event', 'seat'])
                    ->get();

        $sortedTickets = $tickets->sortBy(function ($ticket) {
            return $ticket->event->event_date_at;
        });

        $groupedTickets = $sortedTickets->groupBy('event.title');

        return view('tickets.my-tickets', [
            'groupedTickets' => $groupedTickets
        ]);
    }
}
