<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Workshop;
use App\Models\Student;

class WorkshopDataController extends Controller
{
    //
    public function getFirstData(Request $request)
    {
        // Validate input
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'workshop_id' => 'required|exists:workshops,id',
        ]);

        $studentId = $request->input('student_id');
        $workshopId = $request->input('workshop_id');

        // Get workshop details
        $workshop = Workshop::findOrFail($workshopId);
        $dynamicTableName = 'workshop_' . $workshop->id;

        // Check if the dynamic table exists
        if (!Schema::hasTable($dynamicTableName)) {
            return response()->json([
                'error' => 'Workshop table not found'
            ], 404);
        }

        $columns = ['insurance'];

        $workshopData = DB::table($dynamicTableName)
            ->where('student_id', $studentId)
            ->select($columns)
            ->first();

        if (!$workshopData) {
            return response()->json([
                'error' => 'Student not enrolled in this workshop'
            ], 404);
        }

        return response()->json([
            'workshop_id' => $workshopId,
            'student_id' => $studentId,
            'data' => $workshopData
        ]);
    }

    public function storeSessions(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'session_dates' => 'required|array',
        ]);

        // Validate the number of selected dates
        // $expectedDates = $workshop->number_of_sessions * $workshop->days_per_session;
        // if (count($validated['session_dates']) != $expectedDates) {
        //     return redirect()->back()->withErrors(['session_dates' => "The number of selected dates does not match the expected total of $expectedDates dates."])->withInput();
        // }

        // Store the session dates
        $workshop->session_dates = $validated['session_dates'];
        $workshop->save();

        return redirect()->route('workshops.show', $workshop->id)->with('success', 'Session dates saved successfully!');
    }
    public function saveAnalysis(Request $request, $id)
    {
        $workshop = Workshop::findOrFail($id);
        $analysisInputs = $request->input('analysis', []);
        $sessionNumber = $request->input('session_number', 1);

        foreach ($analysisInputs as $analysisId => $data) {
            $analysisRecord = \DB::table('analysis_' . $workshop->id)->where('id', $analysisId)->first();

            if ($analysisRecord) {
                for ($day = 1; $day <= $workshop->days_per_session; $day++) {
                    $attendanceField = 'day_' . $day . '_attendance';
                    $volunteersField = 'day_' . $day . '_number_of_volunteers';

                    // Calculate attendance and volunteers for each day
                    $attendanceCount = $this->calculateAttendance($workshop->id, $sessionNumber, $day, $analysisRecord->group_number);
                    $volunteersCount = $this->calculateVolunteers($workshop->id, $sessionNumber, $day, $analysisRecord->group_number);

                    $data[$attendanceField] = $attendanceCount;
                    $data[$volunteersField] = $volunteersCount;
                }

                \DB::table('analysis_' . $workshop->id)->where('id', $analysisId)->update($data);
            }
        }

        return redirect()->route('workshops.show', [
            'workshop' => $id,
            'session_number' => $sessionNumber,
        ])->with('success', 'Analysis data saved successfully.');
    }

    protected function calculateAttendance($workshopId, $sessionNumber, $dayNumber, $groupNumber)
    {
        $attendanceColumn = 'session_' . $sessionNumber . '_' . $dayNumber . '_attendance';
        $groupColumn = 'session_' . $sessionNumber . '_' . $dayNumber . '_group_number';

        $count = \DB::table('workshop_' . $workshopId)
            ->where($attendanceColumn, true)
            ->where($groupColumn, $groupNumber)
            ->where('volunteer', false)
            ->count();

        return $count;
    }

    protected function calculateVolunteers($workshopId, $sessionNumber, $dayNumber, $groupNumber)
    {
        $attendanceColumn = 'session_' . $sessionNumber . '_' . $dayNumber . '_attendance';
        $groupColumn = 'session_' . $sessionNumber . '_' . $dayNumber . '_group_number';

        $count = \DB::table('workshop_' . $workshopId)
            ->where($attendanceColumn, true)
            ->where($groupColumn, $groupNumber)
            ->where('volunteer', true)
            ->count();

        return $count;
    }
}
