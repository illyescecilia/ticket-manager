<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class AdminEventController extends Controller
{
    /**
     * Show event creating form.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Save new event to database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date_at' => 'required|date|after:now',
            'sale_start_at' => 'required|date|before:event_date_at',
            'sale_end_at' => 'required|date|after:sale_start_at|before:event_date_at',
            'max_number_allowed' => 'required|integer|min:1',
            'is_dynamic_price' => 'boolean',
            'cover_image' => 'nullable|string',
        ]);

        $validated['is_dynamic_price'] = $request->boolean('is_dynamic_price');
        $validated['cover_image'] = $validated['cover_image'] ?? null;

        Event::create($validated);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Az esemény sikeresen létrejött!');
    }

    /**
     * Show event editing form.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', ['event' => $event]);
    }

    /**
     * Update event in database.
     */
    public function update(Request $request, Event $event)
    {
        $sale_has_started = $event->sale_start_at->isPast();

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|url|max:2048',
        ];

        if (!$sale_has_started) {
            $rules = array_merge($rules, [
                'event_date_at' => 'required|date|after:now',
                'sale_start_at' => 'required|date|after:now',
                'sale_end_at' => 'required|date|after:sale_start_at',
                'max_number_allowed' => 'required|integer|min:1',
                'is_dynamic_price' => 'nullable|boolean',
            ]);
        } else {
             // sale has started
        }

        $validated = $request->validate($rules);

        if ($sale_has_started) {
            $validated['event_date_at'] = $event->event_date_at;
            $validated['sale_start_at'] = $event->sale_start_at;
            $validated['sale_end_at'] = $event->sale_end_at;
            $validated['max_number_allowed'] = $event->max_number_allowed;
            $validated['is_dynamic_price'] = $event->is_dynamic_price;
        } else {
             $validated['is_dynamic_price'] = $request->boolean('is_dynamic_price');
        }

        $validated['cover_image'] = $validated['cover_image'] ?? null;

        $event->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Az esemény sikeresen frissítve lett!');
    }
}
