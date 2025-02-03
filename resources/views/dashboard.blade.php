<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Workshops') }}
        </h2>
    </x-slot>
    <style>
        .table-centered td,
        .table-centered th {
            vertical-align: middle;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="table-responsive">
                    <table class="table table-striped table-centered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date of Beginning</th>
                                <th>Number of Sessions</th>
                                <th>Days per Session</th>
                                <th>Number of Instructors</th>
                                <th>Number of Assistants</th>
                                <th>Number of Groups</th>
                                <th>Fees</th>
                                <th>Insurance</th>
                                <th style="min-width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workshops as $workshop)
                                <tr>
                                    <td>{{ $workshop->name }}</td>
                                    <td>{{ $workshop->date_of_beginning }}</td>
                                    <td>{{ $workshop->number_of_sessions }}</td>
                                    <td>{{ $workshop->days_per_session }}</td>
                                    <td>{{ $workshop->number_of_instructors }}</td>
                                    <td>{{ $workshop->number_of_assistants }}</td>
                                    <td>{{ $workshop->number_of_groups }}</td>
                                    <td>{{ $workshop->fees }}</td>
                                    <td>{{ $workshop->insurance }}</td>
                                    <td>
                                        <a href="{{ route('workshops.show', $workshop->id) }}" class="btn btn-primary">View</a>
                                        <a href="{{ route('workshops.edit', $workshop->id) }}" class="btn btn-info">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>