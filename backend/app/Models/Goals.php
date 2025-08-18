<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Goals extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'completion_date',
    ];

    // Relationship: a goal belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Automatically mark incomplete if past end date and not complete
    public function checkStatus()
    {
        if ($this->status === 'pending' && Carbon::now()->gt(Carbon::parse($this->end_date))) {
            $this->update(['status' => 'incomplete']);
        }
    }
}
