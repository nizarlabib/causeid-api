<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;
    protected $table = 'activities';

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'activity_name',
        'activity_picture',
        'activity_type',
        'activity_kilometers',
        'activity_hours',
        'activity_minutes',
        'activity_seconds',
        'activity_datetime',
        'race_ids',
        'user_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id');
    } 
}
