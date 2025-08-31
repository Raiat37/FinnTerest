<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsApplication extends Model
{
    protected $fillable = [
        'user_id', 'bank_id', 'amount', 'mode', 'status',
        'period_month', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'period_month' => 'date',
        'approved_at'  => 'datetime',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function bank()  { return $this->belongsTo(Bank::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
