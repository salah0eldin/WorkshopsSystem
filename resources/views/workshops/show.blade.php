<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($workshop->name) }}
        </h2>
    </x-slot>
    <div class="container mt-5">
        <h1 class="mb-4">{{ $workshop->name }} Session Dates</h1>

        <!-- Button to show/hide the form -->
        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#formCollapse"
            aria-expanded="false" aria-controls="formCollapse">
            Show/Hide Form
        </button>

        <!-- Collapsible form -->
        <div class="collapse" id="formCollapse">
            <form method="POST" action="{{ route('workshops.storeSessions', $workshop->id) }}">
                @csrf

                <!-- Calendar Grid -->
                <div class="calendar-grid">
                    <!-- Header Row -->
                    <div class="row grid-header">
                        <div class="col day-header">Sat</div>
                        <div class="col day-header">Sun</div>
                        <div class="col day-header">Mon</div>
                        <div class="col day-header">Tue</div>
                        <div class="col day-header">Wed</div>
                        <div class="col day-header">Thu</div>
                        <div class="col day-header">Fri</div>
                    </div>

                    <!-- Session Selection Grid -->
                    @php
                        $startDate = \Carbon\Carbon::parse($workshop->date_of_beginning);
                        $today = \Carbon\Carbon::today();
                    @endphp

                    @for($week = 0; $week < 8; $week++)
                                    <div class="row grid-row">
                                        @for($day = 0; $day < 7; $day++)
                                                            @php
                                                                $currentDate = $startDate->copy()->addDays(($week * 7) + $day);
                                                                $isPast = $currentDate->lt($today);
                                                                $sessionDates = $workshop->session_dates ?? [];
                                                                $checked = in_array($currentDate->format('Y-m-d'), $sessionDates) ? 'checked' : '';
                                                            @endphp

                                                            <div class="col day-cell {{ $isPast ? 'past-day' : '' }}">
                                                                <div class="date-label">
                                                                    {{ $currentDate->format('d M') }}
                                                                </div>
                                                                <div class="session-checkbox">
                                                                    <input type="checkbox" name="session_dates[]" value="{{ $currentDate->format('Y-m-d') }}" {{ $isPast ? 'disabled' : '' }} {{ $checked }}>
                                                                </div>
                                                            </div>
                                        @endfor
                                    </div>
                    @endfor
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Session Dates</button>
                </div>
            </form>
        </div>

        <!-- Session Selection Dropdown -->
        <div class="mt-5">
            <form method="GET" action="{{ route('workshops.show', $workshop->id) }}">
                <div class="form-group">
                    <label for="sessionSelect">Select Session:</label>
                    <select name="session_number" id="sessionSelect" class="form-control" onchange="this.form.submit()">
                        @for($i = 1; $i <= $workshop->number_of_sessions; $i++)
                            <option value="{{ $i }}" {{ ($selectedSession ?? 1) == $i ? 'selected' : '' }}>
                                Session {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="container-fluid mt-4"> <!-- Toggle Button for Analysis Table --> <button class="btn btn-primary mb-3"
            type="button" data-bs-toggle="collapse" data-bs-target="#analysisCollapse" aria-expanded="true"
            aria-controls="analysisCollapse"> Show/Hide Analysis Table </button>
        <!-- Collapsible Analysis Section -->
        <div class="collapse show" id="analysisCollapse">
            <h2>Session {{ $selectedSession }} Analysis</h2>

            <!-- Analysis Table -->
            <form method="POST" action="{{ route('workshops.saveAnalysis', $workshop->id) }}">
                @csrf
                <input type="hidden" name="session_number" value="{{ $selectedSession }}">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <!-- Table Headers -->
                        <thead class="table-primary">
                            <tr>
                                <th rowspan="2">Group Number</th>
                                @for ($day = 1; $day <= $workshop->days_per_session; $day++)
                                    <th colspan="6">Day {{ $day }}
                                        ({{ $sessionDates[($selectedSession - 1) * $workshop->days_per_session + $day - 1] ?? '' }})
                                    </th>
                                @endfor
                            </tr>
                            <tr>
                                @for ($day = 1; $day <= $workshop->days_per_session; $day++)
                                    <th>Instructor</th>
                                    <th>Assistant</th>
                                    <th>Room</th>
                                    <th>Hours</th>
                                    <th>Attendance</th>
                                    <th>Volunteers</th>
                                @endfor
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody>
                            @php
                                $totalAttendance = [];
                                $totalVolunteers = [];
                            @endphp
                            @foreach($analysisData as $analysis)
                                                    <tr>
                                                        <td>{{ $analysis->group_number }}</td>
                                                        @for($day = 1; $day <= $workshop->days_per_session; $day++)
                                                                                    @php
                                                                                        $prefix = 'day_' . $day . '_';
                                                                                        $attendanceField = $prefix . 'attendance';
                                                                                        $volunteersField = $prefix . 'number_of_volunteers';

                                                                                        if (!isset($totalAttendance[$day])) {
                                                                                            $totalAttendance[$day] = 0;
                                                                                            $totalVolunteers[$day] = 0;
                                                                                        }

                                                                                        $attendanceValue = $analysis->$attendanceField ?? 0;
                                                                                        $volunteersValue = $analysis->$volunteersField ?? 0;

                                                                                        $totalAttendance[$day] += $attendanceValue;
                                                                                        $totalVolunteers[$day] += $volunteersValue;
                                                                                    @endphp
                                                                                    <td>
                                                                                        <input type="text" name="analysis[{{ $analysis->id }}][{{ $prefix }}instructor]"
                                                                                            value="{{ $analysis->{$prefix . 'instructor'} }}" class="form-control">
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" name="analysis[{{ $analysis->id }}][{{ $prefix }}assistant]"
                                                                                            value="{{ $analysis->{$prefix . 'assistant'} }}" class="form-control">
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" name="analysis[{{ $analysis->id }}][{{ $prefix }}room]"
                                                                                            value="{{ $analysis->{$prefix . 'room'} }}" class="form-control">
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number" step="0.5"
                                                                                            name="analysis[{{ $analysis->id }}][{{ $prefix }}number_of_hours]"
                                                                                            value="{{ $analysis->{$prefix . 'number_of_hours'} }}" class="form-control">
                                                                                    </td>
                                                                                    <td>{{ $attendanceValue }}</td>
                                                                                    <td>{{ $volunteersValue }}</td>
                                                        @endfor
                                                    </tr>
                            @endforeach
                        </tbody>
                        <!-- Totals Row -->
                        <tfoot>
                            <tr class="text-center">
                                <th>Total</th>
                                @for($day = 1; $day <= $workshop->days_per_session; $day++)
                                    <th colspan="4"></th>
                                    <th>{{ $totalAttendance[$day] ?? 0 }}</th>
                                    <th>{{ $totalVolunteers[$day] ?? 0 }}</th>
                                @endfor
                            </tr>
                            <tr class="text-center">
                                <th>Total Attendance</th>
                                <th colspan="{{6*$workshop->days_per_session}}">
                                    {{ array_sum($totalAttendance) + array_sum($totalVolunteers) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Save Button -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save Analysis Data</button>
                </div>
            </form>
        </div>

        <!-- Session Details Table (Always Visible) -->
        <h2>Session {{ $selectedSession ?? 1 }} Details</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-primary">
                    <tr style="border-top: 4px solid #343a40;">
                        <th colspan="4" class="section-divider" style="border-left: 4px solid #343a40;">Student Info
                        </th>
                        @for($day = 1; $day <= $workshop->days_per_session; $day++)
                            <th colspan="5" class="section-divider">Day {{ $day }}
                                ({{ $sessionDates[($selectedSession - 1) * $workshop->days_per_session + $day - 1] ?? '' }})
                            </th>
                        @endfor
                    </tr>
                    <tr>
                        <th style="border-left: 4px solid #343a40;">Student ID</th>
                        <th>Student Name</th>
                        <th>Volunteer</th>
                        <th class="section-divider">Insurance</th>
                        @for($day = 1; $day <= $workshop->days_per_session; $day++)
                            <th>Attendance</th>
                            <th>Group Number</th>
                            <th>Paid Money</th>
                            <th>Assignment</th>
                            <th class="section-divider">Bonus</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                                    <tr>
                                        <td style="border-left: 4px solid #343a40;">{{ $student->student_id }}</td>
                                        <td>{{ $student->student_name }}</td>
                                        <td>{{ $student->volunteer ? '✔️' : '' }}</td>
                                        <td class="section-divider">{{ $student->insurance ? '✔️' : '' }}</td>
                                        @for($day = 1; $day <= $workshop->days_per_session; $day++)
                                                            @php
                                                                $sessionNumber = $selectedSession ?? 1;
                                                                $attendanceField = 'session_' . $sessionNumber . '_' . $day . '_attendance';
                                                                $groupNumberField = 'session_' . $sessionNumber . '_' . $day . '_group_number';
                                                                $paidMoneyField = 'session_' . $sessionNumber . '_' . $day . '_paid_money';
                                                                $assignmentDegreeField = 'session_' . $sessionNumber . '_' . $day . '_assignment_degree';
                                                                $bonusDegreesField = 'session_' . $sessionNumber . '_' . $day . '_bonus_degrees';

                                                                $attendance = $student->$attendanceField ?? null;
                                                                $groupNumber = $student->$groupNumberField ?? null;
                                                                $paidMoney = $student->$paidMoneyField ?? null;
                                                                $assignmentDegree = $student->$assignmentDegreeField ?? null;
                                                                $bonusDegrees = $student->$bonusDegreesField ?? null;
                                                            @endphp
                                                            <td>{{ $attendance ? '✔️' : '' }}</td>
                                                            <td>{{ $groupNumber ?? '' }}</td>
                                                            <td>{{ $paidMoney ?? '' }}</td>
                                                            <td>{{ $assignmentDegree ?? '' }}</td>
                                                            <td class="section-divider">{{ $bonusDegrees ?? '' }}</td>
                                        @endfor
                                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="{{ 14 }}" style="padding-bottom: 200px;"></td>
                    </tr></tr>
                </tbody>
            </table>
        </div>
    </div>
    <style>
        .calendar-grid {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            padding: 1.3rem;
        }

        .grid-header,
        .grid-row {
            border-bottom: 1px solid #dee2e6;
        }

        .day-header {
            padding: 1rem;
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .day-cell {
            padding: 1.5rem;
            min-height: 100px;
            border-right: 1px solid #dee2e6;
            position: relative;
            background: #fff;
        }

        .past-day {
            background-color: #f8f9fa;
            opacity: 0.6;
        }

        .session-checkbox {
            position: absolute;
            bottom: 5px;
            right: 5px;
        }

        .session-checkbox input {
            transform: scale(1.3);
        }

        .date-label {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
        }

        .table thead th,
        .table tbody td {
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        .table thead th.section-divider,
        .table tbody td.section-divider {
            border-right: 4px solid #343a40;
        }
    </style>
</x-app-layout>