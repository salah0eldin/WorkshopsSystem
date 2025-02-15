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
                <div class="row mt-2">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </div>
                </div>
            </form>

            <!-- Delete Form moved outside update form -->
            <form action="{{ route('students.destroy', $student->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');"
                class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Student</button>
            </form>
        </div>
    </div>
</x-app-layout>