<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_of_beginning',
        'number_of_sessions',
        'days_per_session',
        'number_of_instructors',
        'number_of_assistants',
        'number_of_groups',
        'fees',
        'insurance',
        'session_dates',
    ];

    // Accessor: Decode JSON when getting the value
    public function getSessionDatesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Mutator: Encode array to JSON when setting the value
    public function setSessionDatesAttribute($value)
    {
        $this->attributes['session_dates'] = json_encode($value);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}