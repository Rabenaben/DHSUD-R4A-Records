<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $fileLocation = $type === 'hoa' ? 'HOA Records' : 'REM - ' . $record->province->province_name;
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
        $fileLocation = $type === 'hoa' ? 'HOA Records' : 'REM - ' . $record->province->province_name;
        $this->logActivity($docketNo, $files[$fileIndex]['name'], $fileLocation, 'Unarchive');

        return response()->json(['success' => true]);
    }

    // 🔹 Download File
    public function downloadFile($type, $docketNo, $fileIndex)
    {
        // Convert to lowercase to handle both uppercase and lowercase type parameters
        $type = strtolower($type);

        // Trim docketNo to remove any whitespace
        $docketNo = trim($docketNo);

        if ($type === 'hoa') {
            $record = HoaDatabase::where('docket_no', $docketNo)->first();
        } elseif ($type === 'rem') {
            $record = RemDatabase::where('docket_no', $docketNo)->first();
        }

        if (!$record) {
            abort(404, 'Record not found.');
        }

        $files = json_decode($record->files, true) ?? [];

        // Handle both numeric index and string index
        $numericIndex = intval($fileIndex);

        if (!isset($files[$fileIndex]) && !isset($files[$numericIndex])) {
            abort(404, 'File not found.');
        }

        // Use numeric index if string index doesn't work
        $file = isset($files[$fileIndex]) ? $files[$fileIndex] : $files[$numericIndex];

        if (!isset($file['path'])) {
            abort(404, 'File not found.');
        }

        $path = $file['path'];

        // Use Storage facade like FileControllerTrait
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        // Use original_name if available, otherwise use name
        $downloadName = $file['original_name'] ?? $file['name'] ?? 'download.pdf';

        return response()->download(Storage::disk('local')->path($path), $downloadName);
    }
}
