<?php

namespace App\Http\Controllers;

use App\Models\Bank;

class BankController extends Controller
{
    public function index()
    {
        return Bank::where('is_active', true)
            ->orderBy('name')
            ->get(['id','name','website_url']);
    }
}
