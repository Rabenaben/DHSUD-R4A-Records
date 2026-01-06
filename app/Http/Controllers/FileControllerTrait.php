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

        // Add index to each file for safe identification
        $filesWithIndex = array_map(function ($file, $index) {
            $file['index'] = $index;
            return $file;
        }, $files, array_keys($files));

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
                $path = $file->storeAs($this->folder, $fileName, 'local');

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
