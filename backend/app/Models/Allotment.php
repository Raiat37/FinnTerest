<?php

namespace App\Models;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Allotment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'allotment',
        'description',
        'start_date',
        'end_date',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
