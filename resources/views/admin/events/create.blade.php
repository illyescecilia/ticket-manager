<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új esemény létrehozása') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Hiba történt!</strong>
                        <span class="block sm:inline">Kérlek, javítsd a következő hibákat:</span>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.events.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="title" class="block font-medium text-sm text-gray-700">
                                Cím
                            </label>
                            <input id="title" type="text" name="title" value="{{ old('title') }}" autofocus class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block font-medium text-sm text-gray-700">
                                Leírás (Opcionális)
                            </label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="event_date_at" class="block font-medium text-sm text-gray-700">
                                Esemény időpontja
                            </label>
                            <input id="event_date_at" type="datetime-local" name="event_date_at" value="{{ old('event_date_at') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label for="sale_start_at" class="block font-medium text-sm text-gray-700">
                                Jegyértékesítés kezdete
                            </label>
                            <input id="sale_start_at" type="datetime-local" name="sale_start_at" value="{{ old('sale_start_at') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label for="sale_end_at" class="block font-medium text-sm text-gray-700">
                                Jegyértékesítés vége
                            </label>
                            <input id="sale_end_at" type="datetime-local" name="sale_end_at" value="{{ old('sale_end_at') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label for="max_number_allowed" class="block font-medium text-sm text-gray-700">
                                Maximum engedélyezett jegyek (egy felhasználónak)
                            </label>
                            <input id="max_number_allowed" type="number" name="max_number_allowed" value="{{ old('max_number_allowed', 1) }}" min="1" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div class="md:col-span-2 flex items-center mt-2">
                            <input id="is_dynamic_price" type="checkbox" name="is_dynamic_price" value="1" @checked(old('is_dynamic_price')) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="is_dynamic_price" class="ml-2 block font-medium text-sm text-gray-700">
                                Dinamikus árazás engedélyezése
                            </label>
                        </div>

                         <div class="md:col-span-2">
                            <label for="cover_image" class="block font-medium text-sm text-gray-700">
                                Borítókép URL (Opcionális)
                            </label>
                            <input id="cover_image" type="text" name="cover_image" value="{{ old('cover_image') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                            Mégsem
                        </a>
                        <x-primary-button>
                            {{ __('Esemény létrehozása') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
