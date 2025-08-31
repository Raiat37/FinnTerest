<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['name', 'website_url', 'is_active'];

    public function savingsApplications()
    {
        return $this->hasMany(SavingsApplication::class);
    }
}
