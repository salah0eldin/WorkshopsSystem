<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Money Analysis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            <h3 class="text-lg font-semibold mb-4">Workshop Transactions</h3>
            @foreach($workshops as $workshop)
                <div class="mb-6">
                    <h4 class="text-md font-semibold">{{ $workshop->name }}</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                @for ($i = 1; $i <= $workshop->number_of_sessions; $i++)
                                    @for ($j = 1; $j <= $workshop->days_per_session; $j++)
                                        <th>Session {{ $i }} Day {{ $j }}</th>
                                    @endfor
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions[$workshop->id] as $transaction)
                                <tr>
                                    <td>{{ $transaction->student_id }}</td>
                                    <td>{{ $transaction->student_name }}</td>
                                    @for ($i = 1; $i <= $workshop->number_of_sessions; $i++)
                                        @for ($j = 1; $j <= $workshop->days_per_session; $j++)
                                            <td>{{ $transaction->{'session_' . $i . '_' . $j . '_paid_money'} }}</td>
                                        @endfor
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <h3 class="text-lg font-semibold mb-4">Total Transactions</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Pocket Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totalTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->name }}</td>
                            <td>{{ $transaction->pocket }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
