<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DseClient {
    public function latest() {
        try {
            return Http::timeout(8)->get('http://localhost:3000/v1/dse/latest')->json();
        } catch (\Exception $e) {
            return [];
        }
    }
}
