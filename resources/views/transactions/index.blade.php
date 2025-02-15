<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Money Transactions Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            <pre>
                @foreach($logs as $log)
                    {{ $log }}
                @endforeach
            </pre>
        </div>
    </div>
</x-app-layout>