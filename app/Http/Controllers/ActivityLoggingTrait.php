<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait ActivityLoggingTrait
{
    protected function logActivity($docketNo, $fileName = null, $fileLocation, $action)
    {
        ActivityLog::create([
            'docket_no' => $docketNo,
            'file_name' => $fileName,
            'file_location' => $fileLocation,
            'action' => $action,
            'user_id' => Auth::id(),
        ]);
    }
}