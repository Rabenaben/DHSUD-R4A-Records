<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityController extends Controller
{
    public function getLogs()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json($logs);
    }
}