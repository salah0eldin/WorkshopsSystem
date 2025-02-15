<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class MoneyAnalysisController extends Controller
{
    public function index()
    {
        $workshops = Workshop::all();
        $transactions = [];

        foreach ($workshops as $workshop) {
            $columns = ['student_id', 'student_name'];
            for ($i = 1; $i <= $workshop->number_of_sessions; $i++) {
                for ($j = 1; $j <= $workshop->days_per_session; $j++) {
                    $columns[] = 'session_' . $i . '_' . $j . '_paid_money';
                }
            }

            $workshopTransactions = DB::table('workshop_' . $workshop->id)
                ->select($columns)
                ->get();

            $transactions[$workshop->id] = $workshopTransactions;
        }

        $totalTransactions = DB::table('students')
            ->select('id', 'name', 'pocket')
            ->get();

        return view('money-analysis.index', compact('workshops', 'transactions', 'totalTransactions'));
    }
}
