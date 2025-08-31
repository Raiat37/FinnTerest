<?php

namespace App\Http\Controllers;

use App\Services\DseClient;

class StockController extends Controller
{
    protected $dseClient;

    public function __construct(DseClient $dseClient)
    {
        $this->dseClient = $dseClient;
    }

    public function getLatestStockData()
    {
        return response()->json($this->dseClient->latest());
    }
}
