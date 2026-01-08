<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;
use App\Models\RemDatabase;

class ArchiveController extends Controller
{
    use ActivityLoggingTrait;

    // 🔹 Archive File
    public function archiveFile($type, $docketNo, $fileIndex)
    {
        if ($type === 'hoa') {
            $record = HoaDatabase::where('docket_no', $docketNo)->first();
        } elseif ($type === 'rem') {
            $record = RemDatabase::where('docket_no', $docketNo)->first();
        }

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $files = json_decode($record->files, true) ?? [];

        if (!isset($files[$fileIndex])) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

        $files[$fileIndex]['archived'] = true;

        $record->files = json_encode($files);
        $record->save();

        // Log activity
        $fileLocation = $type === 'hoa' ? 'HOA Records' : 'REM - ' . $record->province;
        $this->logActivity($docketNo, $files[$fileIndex]['name'], $fileLocation, 'Archive');

        return response()->json(['success' => true]);
    }

    // 🔹 Unarchive File
    public function unarchiveFile($type, $docketNo, $fileIndex)
    {
        if ($type === 'hoa') {
            $record = HoaDatabase::where('docket_no', $docketNo)->first();
        } elseif ($type === 'rem') {
            $record = RemDatabase::where('docket_no', $docketNo)->first();
        }

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $files = json_decode($record->files, true) ?? [];

        if (!isset($files[$fileIndex])) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

        $files[$fileIndex]['archived'] = false;

        $record->files = json_encode($files);
        $record->save();

        // Log activity
        $fileLocation = $type === 'hoa' ? 'HOA Records' : 'REM - ' . $record->province;
        $this->logActivity($docketNo, $files[$fileIndex]['name'], $fileLocation, 'Unarchive');

        return response()->json(['success' => true]);
    }
}
