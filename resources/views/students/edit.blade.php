<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Student') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $student->name }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $student->phone }}"
                        required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="volunteer" name="volunteer" {{ $student->volunteer ? 'checked' : '' }}>
                    <label class="form-check-label" for="volunteer">Volunteer</label>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Update Student</button>
            </form>
        </div>
    </div>
</x-app-layout>
