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

    public function updateCash(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:deposit,withdraw',
        ]);

        $student = Student::findOrFail($request->id);
        $amount = $request->amount;
        $type = $request->type;

        if ($type == 'deposit') {
            $student->pocket += $amount;
        } elseif ($type == 'withdraw') {
            $student->pocket -= $amount;
        }

        $student->save();

        $logMessage = now() . " - StudentID: {$student->id} ";
        $logMessage .= ($type == 'deposit' ? "Deposited" : "Withdrew") . " {$amount}. ";
        $logMessage .= "New pocket balance: {$student->pocket}" . PHP_EOL;
        file_put_contents(storage_path('logs/money_transactions.log'), $logMessage, FILE_APPEND);

        return redirect()->route('students.cash')->with('success', 'Student pocket updated successfully! New pocket balance: ' . $student->pocket);
    }

    public function transactions()
    {
        $filePath = storage_path('logs/money_transactions.log');
        $logs = file_exists($filePath) ? file($filePath, FILE_IGNORE_NEW_LINES) : [];
        return view('transactions.index', compact('logs'));
    }

    public function cash(){
        $students = Student::all();
        return view('students.cash', compact('students'));
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }
}
