<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GettingInController extends Controller
{
    public function index()
    {
        $students = Student::all();
        $workshops = Workshop::all();
        $selected_workshop = $workshops->first()->id;
        return view('getting-in.index', compact('students', 'workshops', 'selected_workshop'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'student_id' => 'required|exists:students,id',
            'payAmount' => 'required|numeric|min:0.00',
            'group_number' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $workshop = Workshop::findOrFail($validated['workshop_id']);
        $student = Student::findOrFail($validated['student_id']);
        $payAmount = $validated['payAmount'];
        $groupNumber = $validated['group_number'];

        // Check if today is a workshop day
        $today = Carbon::today();
        $sessionDates = $workshop->session_dates ?? [];

        if (!in_array($today->format('Y-m-d'), $sessionDates)) {
            return redirect()->back()->withErrors(['error' => 'Today is not a workshop day.']);
        }

        // Determine session and day numbers
        $index = array_search($today->format('Y-m-d'), $sessionDates);
        $sessionNumber = intdiv($index, $workshop->days_per_session) + 1;
        $dayNumber = ($index % $workshop->days_per_session) + 1;

        // Check if attendance is already marked for any day in the current session
        for ($day = 1; $day <= $workshop->days_per_session; $day++) {
            $attendanceField = 'session_' . $sessionNumber . '_' . $day . '_attendance';
            $existingAttendance = DB::table('workshop_' . $workshop->id)
                ->where('student_id', $student->id)
                ->value($attendanceField);

            if ($existingAttendance) {
                return redirect()->back()->withErrors(['error' => 'Attendance is already marked for this session.']);
            }
        }

        // Calculate total fees
        if (!$student->volunteer) {
            $totalFees = $workshop->fees + ($request->has('insurance') ? $workshop->insurance : 0);

            // Check payment and update pocket
            $student->pocket += $payAmount - $totalFees;
        }
        $student->{'workshop_' . $workshop->id} = true;
        $student->save();

        // Update or create attendance record
        DB::table('workshop_' . $workshop->id)->updateOrInsert(
            ['student_id' => $student->id],
            [
                'student_name' => $student->name,
                'volunteer' => $student->volunteer,
                'insurance' => DB::raw("CASE WHEN insurance = 1 THEN 1 ELSE " . ($request->has('insurance') ? 1 : 0) . " END"),
                'session_' . $sessionNumber . '_' . $dayNumber . '_attendance' => true,
                'session_' . $sessionNumber . '_' . $dayNumber . '_paid_money' => $payAmount,
                'session_' . $sessionNumber . '_' . $dayNumber . '_group_number' => $groupNumber,
            ]
        );

        return redirect()->route('getting-in.index', ['selected_workshop'])->with('success', 'Student enrolled and attendance marked.');
    }
}