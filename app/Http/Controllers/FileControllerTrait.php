<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

trait FileControllerTrait
{
    protected $model;
    protected $folder;
    protected $recordType;

    public function getFiles($docketNo)
    {
        $record = $this->model::where('docket_no', $docketNo)->first();

        if (!$record) {
            return response()->json(['files' => []]);
        }

        $files = json_decode($record->files, true) ?? [];

        // Filter out archived files
        $filteredFiles = array_filter($files, function ($file) {
            return !isset($file['archived']) || !$file['archived'];
        });

        // Add index to each file for safe identification
        $filesWithIndex = array_map(function ($file, $index) {
            $file['index'] = $index;
            return $file;
        }, $filteredFiles, array_keys($filteredFiles));

        return response()->json(['files' => $filesWithIndex]);
    }

    public function uploadFile(Request $request, $docketNo)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:10240', // 10MB max per file
        ]);

        $record = $this->model::where('docket_no', $docketNo)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => $this->recordType . ' record not found.']);
        }

        // Get existing files
        $files = json_decode($record->files, true) ?? [];

        $uploadedFiles = [];
        $errors = [];

        // Process each uploaded file
        foreach ($request->file('files') as $file) {
            try {
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                // Create a subfolder named after the docket number
                $path = $file->storeAs($this->folder . '/' . $docketNo, $fileName, 'local');

                // Add new file
                $files[] = [
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path' => $path,
                    'date_added' => now('Asia/Manila')->toDateTimeString(),
                    'original_name' => $file->getClientOriginalName(),
                    'last_updated_by' => Auth::check() ? Auth::user()->name : 'Unknown',
                ];

                $uploadedFiles[] = $file->getClientOriginalName();
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        // Update the record
        $record->files = json_encode($files);
        $record->save();

        // Log activity for uploaded files
        $fileLocation = $this->recordType == 'HOA' ? 'HOA Records' : 'REM - ' . $record->province->province_name;
        foreach ($uploadedFiles as $fileName) {
            $this->logActivity($docketNo, $fileName, $fileLocation, 'Uploaded a file');
        }

        $message = count($uploadedFiles) . ' file(s) uploaded successfully.';
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function downloadFile($docketNo, $fileIndex)
    {
        $record = $this->model::where('docket_no', $docketNo)->first();

        if (!$record) {
            abort(404, $this->recordType . ' record not found.');
        }

        $files = json_decode($record->files, true) ?? [];

        if (!isset($files[$fileIndex])) {
            abort(404, 'File not found.');
        }

        $file = $files[$fileIndex];
        $path = $file['path'];

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found on disk.');
        }

        return response()->download(Storage::disk('local')->path($path), $file['original_name']);
    }

    public function renameFile(Request $request, $docketNo, $fileIndex)
    {
        $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        $record = $this->model::where('docket_no', $docketNo)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => $this->recordType . ' record not found.']);
        }

        $files = json_decode($record->files, true) ?? [];

        if (!isset($files[$fileIndex])) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

        $oldName = $files[$fileIndex]['name'];
        $files[$fileIndex]['name'] = $request->new_name;
        $files[$fileIndex]['last_updated_by'] = Auth::check() ? Auth::user()->name : 'Unknown';

        $record->files = json_encode($files);
        $record->save();

        // Log activity
        $fileLocation = $this->recordType == 'HOA' ? 'HOA Records' : 'REM - ' . $record->province->province_name;
        $this->logActivity($docketNo, $request->new_name, $fileLocation, 'Renamed a file from "' . $oldName . '"');

        return response()->json(['success' => true, 'message' => 'File renamed successfully.']);
    }

    public function previewFile($docketNo, $fileIndex)
    {
        $record = $this->model::where('docket_no', $docketNo)->first();

        if (!$record) {
            abort(404, $this->recordType . ' record not found.');
        }

        $files = json_decode($record->files, true) ?? [];

        if (!isset($files[$fileIndex])) {
            abort(404, 'File not found.');
        }

        $file = $files[$fileIndex];
        $path = $file['path'];

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found on disk.');
        }

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file['original_name'] . '"'
        ]);
    }
}
