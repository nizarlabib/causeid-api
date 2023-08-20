<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Races extends Model
{
    use HasFactory;
    protected $table = 'races';

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'race_name',
        'race_picture',
        'race_startdatetime',
        'race_enddatetime',
        'race_activitystartdatetime',
        'race_activityenddatetime',
        'race_description',
        'race_finishkilometer',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    public function raceregistrations()
    {
        return $this->belongsToMany(Race_registrations::class, 'race_registrations', 'user_id', 'race_id');
    }
}
