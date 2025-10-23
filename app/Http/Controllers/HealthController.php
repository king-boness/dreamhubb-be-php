<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function health()
    {
        return response()->json(['ok' => true, 'service' => 'backend', 'ts' => now()], 200);
    }

    public function dbTest()
    {
        try {
            // Ľahký dotaz, ktorý funguje v každej PG DB
            DB::select('select 1');
            return response()->json(['database' => 'connected'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'database' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
