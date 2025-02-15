<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Workshop') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            <form action="{{ route('workshops.update', $workshop->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- First Row: Workshop Name and Date of Beginning -->
                <div class="row">
                    <!-- Workshop Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Workshop Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $workshop->name }}"
                                required>
                        </div>
                    </div>
                    <!-- Date of Beginning -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_beginning">Date of Beginning</label>
                            <input type="date" class="form-control" id="date_of_beginning" name="date_of_beginning"
                                value="{{ $workshop->date_of_beginning }}" required>
                        </div>
                    </div>
                </div>

                <!-- Second Row: Number of Sessions and Days per Session -->
                <div class="row">
                    <!-- Number of Sessions -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_sessions">Number of Sessions</label>
                            <input type="number" class="form-control" id="number_of_sessions" name="number_of_sessions"
                                value="{{ $workshop->number_of_sessions }}" required>
                        </div>
                    </div>
                    <!-- Days per Session -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="days_per_session">Days per Session</label>
                            <input type="number" class="form-control" id="days_per_session" name="days_per_session"
                                value="{{ $workshop->days_per_session }}" required>
                        </div>
                    </div>
                </div>

                <!-- Third Row: Number of Instructors and Number of Assistants -->
                <div class="row">
                    <!-- Number of Instructors -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_instructors">Number of Instructors</label>
                            <input type="number" class="form-control" id="number_of_instructors"
                                name="number_of_instructors" value="{{ $workshop->number_of_instructors }}" required>
                        </div>
                    </div>
                    <!-- Number of Assistants -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_assistants">Number of Assistants</label>
                            <input type="number" class="form-control" id="number_of_assistants"
                                name="number_of_assistants" value="{{ $workshop->number_of_assistants }}" required>
                        </div>
                    </div>
                </div>

                <!-- Fourth Row: Fees and Insurance -->
                <div class="row">
                    <!-- Fees -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fees">Fees</label>
                            <input type="text" class="form-control" id="fees" name="fees" value="{{ $workshop->fees }}"
                                required>
                        </div>
                    </div>
                    <!-- Insurance -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="insurance">Insurance</label>
                            <input type="text" class="form-control" id="insurance" name="insurance"
                                value="{{ $workshop->insurance }}" required>
                        </div>
                    </div>
                </div>

                <!-- Fifth Row: Number of Groups -->
                <div class="row">
                    <!-- Number of Groups -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_of_groups">Number of Groups</label>
                            <input type="number" class="form-control" id="number_of_groups" name="number_of_groups"
                                value="{{ $workshop->number_of_groups }}" required>
                        </div>
                    </div>
                    <!-- Empty Column for Alignment -->
                    <div class="col-md-6">
                        <!-- You can add another field here if needed -->
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Workshop</button>
                    </div>
                </div>
            </form>

            <!-- Delete Form moved outside update form -->
            <form action="{{ route('workshops.destroy', $workshop->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this workshop? This action cannot be undone.');"
                class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Workshop</button>
            </form>
        </div>
    </div>
</x-app-layout>