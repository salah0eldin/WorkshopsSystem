<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Workshop;

class StudentsController extends Controller
{
    public function index()
    {
        $students = Student::all();
        $workshops = Workshop::all();
        return view('students.index', compact('students', 'workshops'));
    }
    //
    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);

        // Check if the student already exists
        $existingStudent = Student::where('name', $request->name)
            ->where('phone', $request->phone)
            ->first();

        if ($existingStudent) {
            return redirect()->route('students.create')->with([
                'error' => 'Student already exists!',
                'studentId' => $existingStudent->id
            ]);
        }

        // Assign a new ID
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $newId = $lastStudent ? $lastStudent->id + 1 : 1;

        // Create a new student
        Student::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'id' => $newId,
            'volunteer' => $request->has('volunteer'),
        ]);

        return redirect()->route('students.create')->with([
            'success' => 'Signed up successfully!',
            'studentId' => $newId
        ]);
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);

        // Set volunteer to true if the checkbox is checked, otherwise false
        $validated['volunteer'] = $request->has('volunteer');

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }
    // public function search(Request $request)
    // {
    //     $query = $request->get('query');
    //     $field = $request->get('field');

    //     // Validate the field to ensure it's one of the allowed columns
    //     if (!in_array($field, ['name', 'phone', 'id'])) {
    //         return response()->json([], 400); // Return an empty response with a 400 status code
    //     }

    //     $students = Student::where($field, 'LIKE', "%{$query}%")
    //         ->take(5)
    //         ->get();

    //     return response()->json($students);
    // }
}
