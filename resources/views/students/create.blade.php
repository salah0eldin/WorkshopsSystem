<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sign Up') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }} Your ID is: {{ session('studentId') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }} Your ID is: {{ session('studentId') }}
                </div>
            @endif
            <form action="{{ route('students.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name"
                        required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="Enter your phone number" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="volunteer" name="volunteer">
                    <label class="form-check-label" for="volunteer">Volunteer</label>
                </div>
                <button type="submit" class="btn btn-primary mt-1">Submit</button>
            </form>
        </div>
    </div>
</x-app-layout>