<x-guest-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="lg:flex">

                    {{-- cover image --}}
                    <div class="lg:w-1/3 relative h-96 lg:h-auto overflow-hidden rounded-t-lg lg:rounded-l-lg lg:rounded-t-none flex-shrink-0 bg-gray-300">
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
                    </div>

                    {{-- event details --}}
                    <div class="lg:w-2/3 p-6 sm:p-8 space-y-8 flex flex-col justify-between min-w-0">

                        <div>
                            <div class="flex justify-between items-start mb-6 border-b dark:border-gray-700 pb-3">
                                {{-- title --}}
                                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white leading-tight mb-6 border-b dark:border-gray-700 pb-3">
                                    {{ $event->title }}
                                </h1>

                                {{-- edit button (admin only) --}}
                                @auth
                                    @if (auth()->user()->admin ?? false)
                                        <a href="{{ route('admin.events.edit', $event) }}"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4 flex-shrink-0">
                                            Szerkesztés
                                        </a>
                                    @endif
                                @endauth
                            </div>

                            {{-- date and buy button --}}
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center pb-4 space-y-4 md:space-y-0">

                                <div class="mb-4 md:mb-0">
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Időpont:</p>
                                    <p class="text-2xl text-indigo-600 dark:text-indigo-400 font-bold">{{ $event->event_date_at->format('Y. F j. H:i') }}</p>
                                </div>

                                <div class="w-full md:w-auto">
                                    @php
                                        $now = now();
                                        $saleStarted = $now->greaterThanOrEqualTo($event->sale_start_at);
                                        $saleEnded = $now->greaterThan($event->sale_end_at);
                                        $isAvailable = $saleStarted && !$saleEnded;
                                    @endphp

                                    @if ($isAvailable)
                                        <a href="{{ route('events.purchase.create', $event) }}"
                                            class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition duration-300 transform hover:scale-105 block text-center">
                                            Jegyvásárlás
                                        </a>
                                        <p class="text-sm text-green-600 dark:text-green-400 mt-2 text-center">Az értékesítés {{ $event->sale_end_at->format('Y. F j. H:i') }}-ig tart.</p>
                                    @elseif ($saleEnded)
                                        <button disabled
                                                class="px-6 py-3 bg-red-700 text-white font-bold rounded-lg cursor-not-allowed opacity-75 w-full">
                                            Értékesítés Lezárva
                                        </button>
                                        <p class="text-sm text-red-600 dark:text-red-400 mt-2 text-center">Az értékesítés lezárult.</p>
                                    @else
                                        <button disabled
                                                class="px-6 py-3 bg-yellow-500 text-gray-900 font-bold rounded-lg cursor-not-allowed opacity-75 w-full">
                                            Még nem indult
                                        </button>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-2 text-center">Az értékesítés {{ $event->sale_start_at->format('Y. F j. H:i') }}-kor indul.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- description --}}
                            <div class="mt-8 border-t dark:border-gray-700 pt-4">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 pb-2">Részletes leírás</h2>
                                <div class="text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                                    {{ $event->description }}
                                </div>
                            </div>
                        </div>

                        {{-- information --}}
                        <div class="mt-8 pt-4 border-t dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">További információk</h2>
                            <ul class="text-gray-600 dark:text-gray-400 space-y-2">
                                <li class="flex justify-between items-start">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Dinamikus árazás:</span>
                                    <span class="text-right">{{ $event->is_dynamic_price ? 'Igen' : 'Nem' }}</span>
                                </li>
                                <li class="flex justify-between items-start">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Max. vásárolható jegy/fő:</span>
                                    <span class="text-right">{{ $event->max_number_allowed }} db</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
