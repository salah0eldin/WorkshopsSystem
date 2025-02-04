<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Students') }}
        </h2>
    </x-slot>

    <style>
        /* Adjust the table container to allow scrolling */
        .table-responsive {
            max-height: 75vh;
            /* Set a max height for the table container */
            overflow-y: auto;
            /* Enable vertical scrolling */
            overflow-x: auto;
            /* Enable horizontal scrolling if needed */
        }

        /* Make the header row sticky */
        .table thead th {
            position: sticky;
            top: 0;
            background-color: #fff;
            /* Set background color for header */
            z-index: 2;
            /* Ensure header stays above table body */
        }

        /* Optional: Add a box-shadow to the header when scrolling */
        .table thead th {
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }

        /* Adjust the 'Name' column */
        .table th.name-column,
        .table td.name-column {
            min-width: 150px;
            /* Set your desired minimum width */
            max-width: 300px;
            /* Set your desired maximum width */
            word-wrap: break-word;
            /* Allow text to wrap if it exceeds max-width */
            text-align: left;
            /* Left align the content */
        }
    </style>

    <div class="py-12">
        <div class="container mx-auto">
            <div class="d-flex justify-content-center">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <!-- <th style="min-width: 300px;">Name</th> -->
                                <th class="name-column">Name</th>
                                <th>Phone Number</th>
                                <th>Volunteer</th>
                                <th>Pocket</th>
                                @foreach($workshops as $workshop)
                                    <th>{{ $workshop->name }}</th>
                                @endforeach
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td class="name-column">{{ $student->name }}</td>
                                    <td>{{ $student->phone }}</td>
                                    <td>{{ $student->volunteer ? '✔️' : '' }}</td>
                                    <td>{{ $student->pocket ?? '' }}</td>
                                    @foreach($workshops as $workshop)
                                        <td>{{ $student->{'workshop_' . $workshop->id} ? '✔️' : '' }}</td>
                                    @endforeach
                                    <td>
                                        <a href="{{ route('students.edit', $student->id) }}"
                                            class="btn btn-info btn-sm">Edit</a>
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