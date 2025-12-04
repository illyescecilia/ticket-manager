<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Irányítópult') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Sikeres művelet!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Bezár</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.03a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L5.651 8.849a1.2 1.2 0 1 1 1.697-1.697L10 9.819l2.651-3.03a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15z"/></svg>
                    </span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Hiba történt!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Bezár</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.03a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L5.651 8.849a1.2 1.2 0 1 1 1.697-1.697L10 9.819l2.651-3.03a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15z"/></svg>
                    </span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- summarized statistics --}}
                <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-indigo-500 hover:shadow-xl transition">
                    <p class="text-sm font-medium text-gray-500">
                        Összes esemény
                    </p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ number_format($summary['totalEvents'], 0, ',', ' ') }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500 hover:shadow-xl transition">
                    <p class="text-sm font-medium text-gray-500">
                        Összes eladott jegy
                    </p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ number_format($summary['totalTicketsSold'], 0, ',', ' ') }} db
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-yellow-500 hover:shadow-xl transition">
                    <p class="text-sm font-medium text-gray-500">
                        Befolyt összbevétel
                    </p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ number_format($summary['totalRevenue'], 0, ',', ' ') }} Ft
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500 mb-3">
                        Top 3 kedvelt ülőhely
                    </p>
                    <ul class="space-y-1">
                        @forelse ($summary['topSeats'] as $index => $seat)
                            <li class="flex justify-between text-gray-800">
                                <span class="font-semibold">
                                    {{ $index + 1 }}. Ülőhely: {{ $seat->seat_number }}
                                </span>
                                <span class="font-bold text-indigo-600">
                                    {{ $seat->tickets_sold }} db
                                </span>
                            </li>
                        @empty
                            <li class="text-gray-500 italic text-sm">
                                Még nincs eladott jegy a ranglistához.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- statistics for each event --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">
                    {{ __('Események statisztikái') }}</h3>

                @if ($events->isEmpty())
                    <p class="text-gray-500 italic">
                        Jelenleg nincsenek események a rendszerben.
                    </p>
                @else
                    <div class="space-y-6">
                        @foreach ($events as $event)
                            <div class="border rounded-lg p-5 shadow-md hover:shadow-lg transition">

                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-xl font-bold text-indigo-700">
                                        {{ $event->title }}
                                    </h4>

                                    <div class="flex space-x-2 items-center">
                                        <a href="{{ route('admin.events.edit', $event) }}"
                                            class="inline-flex items-center px-3 py-1 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 focus:outline-none transition ease-in-out duration-150">
                                            Szerkesztés
                                        </a>

                                        @if ($event->sold_tickets == 0)
                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                                                onsubmit="return confirm('Biztosan törölni szeretnéd az eseményt: {{ $event->title }}?');"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150">
                                                    Törlés
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-red-500 text-xs font-semibold px-2 py-1 bg-red-100 rounded-md">
                                                Nem törölhető (jegy eladás)
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border-t pt-4 mt-4">
                                    <div class="flex flex-col border-r">
                                        <span class="text-xs font-medium text-gray-500 uppercase">
                                            Összes ülőhely
                                        </span>
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ $summary['totalSeats'] }} db
                                        </span>
                                    </div>

                                    <div class="flex flex-col border-r">
                                        <span class="text-xs font-medium text-gray-500 uppercase">
                                            Eladott jegyek
                                        </span>
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ $event->sold_tickets }} db
                                        </span>
                                    </div>

                                    <div class="flex flex-col border-r">
                                        <span class="text-xs font-medium text-gray-500 uppercase">
                                            Szabad jegyek
                                        </span>
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ $summary['totalSeats'] - $event->sold_tickets }} db
                                        </span>
                                    </div>

                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500 uppercase">
                                            Eddigi összbevétel
                                        </span>
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ number_format($event->revenue, 0, ',', ' ') }} Ft
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
