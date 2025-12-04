<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jövőbeli események') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($events->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-600">{{ __('Jelenleg nincs meghirdetett jövőbeli esemény.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($events as $event)
                        {{-- variables for showing available tickets --}}
                        @php
                            $soldCount = $soldTickets->get($event->id)->sold_count ?? 0;
                            $totalCapacity = $totalSeats;

                            $remainingSeats = $totalSeats - $soldCount;
                            $fillPercentage = $totalSeats > 0 ? ($soldCount / $totalSeats) * 100 : 100;

                            $statusColor = 'bg-green-500';
                            $statusText = 'Jegyvásárlás';

                            if ($remainingSeats <= 0) {
                                $statusColor = 'bg-red-600';
                                $statusText = 'ELFOGYOTT';
                            } elseif ($fillPercentage >= 75) {
                                $statusColor = 'bg-orange-500';
                            }
                        @endphp

                        {{-- showing events --}}
                        <div class="bg-white overflow-hidden shadow-xl rounded-lg flex flex-col">

                            {{-- cover image and ticket availability --}}
                            <div class="relative h-48 bg-gray-300">
                                @php
                                    $placeholderDomain = 'https://via.placeholder.com';
                                    $imagePath = $event->cover_image;

                                    $isPlaceholder = str_starts_with($imagePath, $placeholderDomain);
                                    $isEmpty = empty($imagePath);

                                    $isLocalFile = !$isEmpty && !str_starts_with($imagePath, 'http');
                                    $localFileExists = false;

                                    if ($isLocalFile) {
                                        $localFileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath);
                                    }

                                    $showFallbackDiv = $isPlaceholder || $isEmpty || ($isLocalFile && !$localFileExists);

                                    if (!$showFallbackDiv) {
                                        if (str_starts_with($imagePath, 'http')) {
                                            $imageUrl = $imagePath;
                                        } else {
                                            $imageUrl = asset('storage/' . $imagePath);
                                        }
                                    }
                                @endphp

                                @if ($showFallbackDiv)
                                    <div class="w-full h-full flex items-center justify-center text-gray-500 font-semibold text-lg">
                                        BORÍTÓKÉP
                                    </div>
                                @else
                                    <img src="{{ $imageUrl }}"
                                        alt="{{ $event->title }} borítóképe"
                                        class="w-full h-full object-cover">
                                @endif

                                <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-300">
                                    <div class="h-full {{ $statusColor }} transition-all duration-500"
                                        style="width: {{ min(100, $fillPercentage) }}%;"
                                        title="{{ round($fillPercentage) }}% telítettség"></div>
                                </div>
                            </div>

                            {{-- event info --}}
                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $event->title }}</h3>

                                {{-- date --}}
                                <p class="text-gray-600 text-sm mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ date('Y. m. d. (D) H:i', strtotime($event->event_date_at)) }}
                                </p>

                                {{-- available tickets --}}
                                <div class="mt-auto pt-4 border-t border-gray-100">
                                    <p class="text-sm font-semibold mb-3">
                                        @if ($remainingSeats > 0)
                                            <span class="text-green-600">{{ $remainingSeats }}</span> szabad jegy
                                            maradt!
                                        @else
                                            <span class="text-red-600 font-bold">{{ __('ELFOGYOTT') }}</span>
                                        @endif
                                    </p>

                                    {{-- ticket info --}}
                                    <a href="{{ route('events.show', $event) }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200 w-full justify-center
                                        @if ($remainingSeats <= 0) bg-gray-400 cursor-not-allowed hover:bg-gray-400
                                        @else
                                            bg-indigo-600 hover:bg-indigo-700 @endif"
                                        @if ($remainingSeats <= 0) onclick="event.preventDefault()" @endif>
                                        {{ $remainingSeats > 0 ? 'Jegy részletek' : $statusText }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
