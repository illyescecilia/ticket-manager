<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Saját jegyeim') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Sikeres vásárlás!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">

                    @if ($groupedTickets->isEmpty())
                        <div class="text-center text-gray-500">
                            <p class="text-lg">Még nincsenek megvásárolt jegyeid.</p>
                            <a href="{{ route('events.index') }}"
                                class="mt-4 inline-block px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition">
                                Vissza az eseményekhez
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($groupedTickets as $eventTitle => $eventTickets)
                                {{-- event info --}}
                                @php
                                    $mainTicket = $eventTickets->first();
                                @endphp

                                <div class="flex flex-col border rounded-lg overflow-hidden shadow-sm transition-all hover:shadow-md">
                                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                                        <h3 class="text-2xl font-bold text-indigo-700 mb-1">
                                            {{ $mainTicket->event->title }}
                                        </h3>
                                        <p class="text-base text-gray-600 mb-2 font-medium">
                                            Esemény időpontja:
                                            {{ date('Y. m. d. H:i', strtotime($mainTicket->event->event_date_at)) }}
                                        </p>
                                        <p class="text-sm font-semibold text-gray-700">
                                            Megvásárolt jegyek száma: {{ $eventTickets->count() }} db
                                        </p>
                                    </div>

                                    {{-- tickets --}}
                                    <div class="p-6">
                                        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                                            Jegyek részletei:
                                        </h4>

                                        <div class="space-y-4">
                                            @foreach ($eventTickets as $ticket)
                                                <div class="border p-4 rounded-lg bg-white shadow-sm hover:shadow-md transition">
                                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                                                        <div class="mb-3 sm:mb-0">
                                                            <p class="text-lg font-bold text-gray-900">
                                                                Ülőhely: <span class="text-indigo-600">{{ $ticket->seat->seat_number }}</span>
                                                            </p>
                                                            <p class="text-md font-semibold text-gray-700">
                                                                Ár: {{ number_format($ticket->price, 0, ',', ' ') }} Ft
                                                            </p>
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                Vásárlás: {{ date('Y. m. d. H:i', strtotime($ticket->created_at)) }}
                                                            </p>
                                                        </div>

                                                        {{-- barcode --}}
                                                        <div class="text-center">
                                                            @php
                                                                $svg = DNS1D::getBarcodeSVG($ticket->barcode, 'C128', 2, 70);
                                                            @endphp
                                                            <div class="mt-2">{!! $svg !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
