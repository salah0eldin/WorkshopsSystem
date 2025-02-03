<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create a New Workshop') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('workshops.store') }}" method="POST">
                @csrf

                <!-- First Row: Workshop Name and Date of Beginning -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Workshop Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter workshop name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_beginning">Date of Beginning</label>
                            <input type="date" class="form-control" id="date_of_beginning" name="date_of_beginning" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Second Row: Number of Sessions and Days per Session -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_sessions">Number of Sessions</label>
                            <input type="number" class="form-control" id="number_of_sessions" name="number_of_sessions" value="10" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="days_per_session">Days per Session</label>
                            <input type="number" class="form-control" id="days_per_session" name="days_per_session" value="1" required>
                        </div>
                    </div>
                </div>

                <!-- Third Row: Number of Instructors and Number of Assistants -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_instructors">Number of Instructors</label>
                            <input type="number" class="form-control" id="number_of_instructors" name="number_of_instructors" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_assistants">Number of Assistants</label>
                            <input type="number" class="form-control" id="number_of_assistants" name="number_of_assistants" value="1" required>
                        </div>
                    </div>
                </div>

                <!-- Fourth Row: Insurance and Fees -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fees">Fees</label>
                            <input type="text" class="form-control" id="fees" name="fees" value="50" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="insurance">Insurance</label>
                            <input type="text" class="form-control" id="insurance" name="insurance" value="120" required>
                        </div>
                    </div>
                </div>

                <!-- Fifth Row: Number of Groups -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_groups">Number of Groups</label>
                            <input type="number" class="form-control" id="number_of_groups" name="number_of_groups" value="2" required>
                        </div>
                    </div>
                    <!-- Empty column to align the single field -->
                    <div class="col-md-6">
                        <!-- You can leave this empty or add another form field if needed -->
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary mt-4">Add Workshop</button>
            </form>
        </div>
    </div>
</x-app-layout>