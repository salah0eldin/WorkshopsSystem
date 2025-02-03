<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workshops = Workshop::all();
        return view('dashboard', compact('workshops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workshops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_beginning' => 'required|date',
            'number_of_sessions' => 'required|integer',
            'days_per_session' => 'required|integer',
            'number_of_instructors' => 'required|integer',
            'number_of_assistants' => 'required|integer',
            'number_of_groups' => 'required|integer',
            'fees' => 'required|numeric',
            'insurance' => 'required|numeric',
        ]);

        try {
            $workshop = Workshop::create($validated);

            // Create a new table for the workshop
            Schema::create('workshop_' . $workshop->id, function (Blueprint $table) use ($workshop) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->string('student_name');
                $table->boolean('volunteer')->default(false);
                $table->boolean('insurance')->default(false);

                for ($i = 1; $i <= $workshop->number_of_sessions; $i++) {
                    for ($j = 1; $j <= $workshop->days_per_session; $j++) {
                        $table->boolean('session_' . $i . '_' . $j . '_attendance')->default(false);
                        $table->integer('session_' . $i . '_' . $j . '_group_number')->nullable();
                        $table->decimal('session_' . $i . '_' . $j . '_paid_money', 8, 2)->default(0);
                        $table->integer('session_' . $i . '_' . $j . '_assignment_degree')->nullable();
                        $table->integer('session_' . $i . '_' . $j . '_bonus_degrees')->nullable();
                    }
                }
                $table->timestamps();
            });

            // Create a new table for the analysis data
            Schema::create('analysis_' . $workshop->id, function (Blueprint $table) use ($workshop) {
                $table->id();
                $table->unsignedInteger('session_number');
                $table->unsignedInteger('group_number');

                // Loop over days per session to create columns for each day
                for ($j = 1; $j <= $workshop->days_per_session; $j++) {
                    $prefix = 'day_' . $j . '_';

                    $table->string($prefix . 'instructor')->nullable();
                    $table->string($prefix . 'assistant')->nullable();
                    $table->string($prefix . 'room')->nullable();
                    $table->decimal($prefix . 'number_of_hours', 5, 2)->nullable();
                    $table->unsignedInteger($prefix . 'attendance')->default(0);
                    $table->unsignedInteger($prefix . 'number_of_volunteers')->default(0);
                }
                $table->timestamps();

                $table->unique(['session_number', 'group_number'], 'unique_analysis_row');
            });

            // Pre-populate the analysis table with rows for each session and group
            $analysisData = [];
            for ($i = 1; $i <= $workshop->number_of_sessions; $i++) {
                for ($g = 1; $g <= 4; $g++) {
                    $analysisData[] = [
                        'session_number' => $i,
                        'group_number' => $g,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            \DB::table('analysis_' . $workshop->id)->insert($analysisData);

            Schema::table('students', function (Blueprint $table) use ($workshop) {
                $table->boolean('workshop_' . $workshop->id)->default(false);
            });

            return redirect()->route('workshops.create')->with('success', 'Workshop created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('workshops.create')->with('error', 'Failed to create workshop. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop)
    {
        // Calculate all session dates
        $startDate = Carbon::parse($workshop->date_of_beginning);

        // Get the selected session number from the request, default to 1
        $selectedSession = request('session_number') ?? 1;

        // Fetch students related to the workshop
        $students = \DB::table('workshop_' . $workshop->id)->get();

        // Fetch **all** analysis data for the workshop
        // Fetch analysis data for the selected session
        $analysisData = \DB::table('analysis_' . $workshop->id)
            ->where('session_number', $selectedSession)
            ->get();

        return view('workshops.show', [
            'workshop' => $workshop,
            'startDate' => $startDate,
            'selectedSession' => $selectedSession,
            'students' => $students,
            'analysisData' => $analysisData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workshop $workshop)
    {
        return view('workshops.edit', compact('workshop'));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_beginning' => 'required|date',
            'number_of_sessions' => 'required|integer',
            'days_per_session' => 'required|integer',
            'number_of_instructors' => 'required|integer',
            'number_of_assistants' => 'required|integer',
            'number_of_groups' => 'required|integer',
            'fees' => 'required|numeric',
            'insurance' => 'required|numeric',
            'session_dates' => 'nullable|array',
        ]);

        // If session_dates is provided, validate the number of dates
        if (isset($validated['session_dates'])) {
            // $expectedDates = $validated['number_of_sessions'] * $validated['days_per_session'];
            // if (count($validated['session_dates']) != $expectedDates) {
            //     return redirect()->back()->withErrors(['session_dates' => "The number of selected dates does not match the expected total of $expectedDates dates."])->withInput();
            // }
        }

        $workshop->update($validated);

        return redirect()->route('workshops.index')->with('success', 'Workshop updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop)
    {
        //
    }
}
