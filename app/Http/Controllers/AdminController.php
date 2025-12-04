<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with the statistics.
     */
    public function dashboard()
    {
        $totalEvents = Event::count();
        $totalTicketsSold = Ticket::count();
        $totalRevenue = Ticket::sum('price');
        $totalSeats = Seat::count();

        $topSeats = DB::table('tickets')
            ->join('seats', 'tickets.seat_id', '=', 'seats.id')
            ->select('seats.seat_number', DB::raw('count(*) as tickets_sold'))
            ->groupBy('seats.seat_number')
            ->orderByDesc('tickets_sold')
            ->limit(3)
            ->get();

        $events = Event::orderBy('event_date_at', 'desc')->paginate(5);

        $events->getCollection()->transform(function ($event) {
            $soldTicketsCount = Ticket::where('event_id', $event->id)->count();
            $eventRevenue = Ticket::where('event_id', $event->id)->sum('price');

            $event->sold_tickets = $soldTicketsCount;
            $event->revenue = $eventRevenue;

            return $event;
        });

        $summary = [
            'totalEvents' => $totalEvents,
            'totalTicketsSold' => $totalTicketsSold,
            'totalRevenue' => $totalRevenue,
            'totalSeats' => $totalSeats,
            'topSeats' => $topSeats,
        ];

        return view('admin.admin-dashboard', [
            'summary' => $summary,
            'events' => $events,
        ]);
    }

    /**
     * Delete event from database.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $soldTicketsCount = Ticket::where('event_id', $event->id)->count();

        if ($soldTicketsCount > 0) {
            return redirect()->route('admin.dashboard')->with('error', 'Az eseményt nem lehet törölni, mert már ' . $soldTicketsCount . ' db jegyet értékesítettek!');
        }

        try {
            $eventTitle = $event->title;
            $event->delete();

            return redirect()->route('admin.dashboard')->with('success', "A(z) '{$eventTitle}' esemény sikeresen törölve lett.");

        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Hiba történt az esemény törlése során.');
        }
    }
}
