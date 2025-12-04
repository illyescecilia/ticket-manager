<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jegyvásárlás') }}: {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Hoppá! Hiba történt.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="purchaseForm" action="{{ route('events.purchase.store', $event) }}" method="POST"
                class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf

                {{-- seat selector --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Helytérkép</h3>
                    <div class="text-sm mb-4 p-3 bg-gray-50 rounded-md">
                        Kattints a szabad helyekre a kiválasztáshoz. A maximálisan választható jegyek száma erre az eseményre:
                        <strong class="text-indigo-600">{{ $remainingUserLimit }} db</strong>.
                    </div>

                    <div class="flex flex-wrap gap-4 mb-6">
                        <div class="flex items-center"><span class="w-5 h-5 bg-gray-200 border rounded mr-2"></span>
                            Szabad</div>
                        <div class="flex items-center"><span class="w-5 h-5 bg-red-500 border rounded mr-2"></span>
                            Foglalt</div>
                        <div class="flex items-center"><span class="w-5 h-5 bg-indigo-600 border border-indigo-800 rounded mr-2"></span>
                            Kiválasztva</div>
                    </div>

                    {{-- seat map --}}
                    <div id="seat-map" class="grid grid-cols-10 md:grid-cols-15 lg:grid-cols-20 gap-2">
                        @foreach ($allSeats as $seat)
                            @php
                                $isTaken = in_array($seat->id, $takenSeatIds);
                                $isDisabled = $isTaken;
                                $price = $seat->base_price;

                                if ($event->is_dynamic_price) {
                                    $price = $seat->base_price * (1 - 0.5 * (1 / ($daysUntil + 1))) * (1 + 0.5 * $occupancy);
                                    $price = round($price);
                                }
                            @endphp

                            {{-- clickable seats --}}
                            <label
                                class="seat-label relative flex items-center justify-center w-10 h-10 text-xs font-bold rounded
                                       border cursor-pointer transition-all duration-150 text-center
                                       {{ $isTaken ? 'bg-red-500 text-white cursor-not-allowed opacity-60' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                                data-seat-id="{{ $seat->id }}" data-seat-price="{{ $price }}"
                                data-seat-number="{{ $seat->seat_number }}" id="label-seat-{{ $seat->id }}">
                                {{ $seat->seat_number }}
                                <input type="checkbox" name="seats[]" value="{{ $seat->id }}"
                                    class="hidden seat-checkbox" {{ $isDisabled ? 'disabled' : '' }}
                                    onchange="handleSeatSelection(this)">
                                <span
                                    class="seat-tooltip absolute hidden -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 z-10 whitespace-nowrap">
                                    {{ $seat->seat_number }} ({{ $price }} Ft)
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- purchase summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-lg sticky top-12">
                        <h3 class="text-2xl font-bold mb-4 border-b pb-2">Összesítő</h3>

                        <div class="mb-4">
                            <h4 class="text-lg font-semibold">{{ $event->title }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ date('Y. m. d. (D) H:i', strtotime($event->event_date_at)) }}</p>
                        </div>

                        <div id="selected-seats-list" class="mb-4">
                            <p id="no-seats-selected" class="text-gray-500">Nincsenek kiválasztott helyek.</p>
                            <ul class="space-y-2">
                                {{-- show selected seats here --}}
                            </ul>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold">Végösszeg:</span>
                                <span id="total-price" class="text-2xl font-bold text-indigo-600">0 Ft</span>
                            </div>

                            <button id="purchase-button" type="submit"
                                class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 disabled:opacity-50"
                                disabled>
                                Vásárlás
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    {{-- css --}}
    <style>
        .seat-label:hover .seat-tooltip {
            display: block;
        }

        .seat-label.selected {
            background-color: #4f46e5;
            color: white;
            border-color: #3730a3;
        }
    </style>

    {{-- js --}}
    <script>
        const maxLimit = {{ $remainingUserLimit }};
        let selectedSeats = new Map();

        const listContainer = document.querySelector('#selected-seats-list');
        const noSeatsP = document.querySelector('#no-seats-selected');
        const totalPriceEl = document.querySelector('#total-price');
        const purchaseButton = document.querySelector('#purchase-button');

        function handleSeatSelection(checkbox) {
            const seatId = checkbox.value;
            const label = document.querySelector(`#label-seat-${seatId}`);

            if (checkbox.checked) {
                if (selectedSeats.size >= maxLimit) {
                    checkbox.checked = false;
                    alert(`Maximum ${maxLimit} jegyet választhatsz ki erre az eseményre.`);
                    return;
                }
                label.classList.add('selected');
                selectedSeats.set(seatId, {
                    number: label.dataset.seatNumber,
                    price: parseInt(label.dataset.seatPrice, 10)
                });
            } else {
                label.classList.remove('selected');
                selectedSeats.delete(seatId);
            }

            updateSummary();
        }

        function updateSummary() {
            listContainer.innerHTML = '';
            if (selectedSeats.size === 0) {
                listContainer.appendChild(noSeatsP);
            } else {
                const ul = document.createElement('ul');
                ul.className = 'space-y-2';

                selectedSeats.forEach((seat, id) => {
                    const li = document.createElement('li');
                    li.className = 'flex justify-between text-sm';
                    li.innerHTML =
                        `<span>Hely: <strong class="font-semibold">${seat.number}</strong></span> <span class="font-semibold">${seat.price} Ft</span>`;
                    ul.appendChild(li);
                });
                listContainer.appendChild(ul);
            }

            let totalPrice = 0;
            selectedSeats.forEach(seat => {
                totalPrice += seat.price;
            });
            totalPriceEl.textContent = `${totalPrice} Ft`;

            purchaseButton.disabled = selectedSeats.size === 0;
        }

        updateSummary();
    </script>
</x-app-layout>
