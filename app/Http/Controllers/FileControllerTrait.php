<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $record = $this->model::where('docket_no', $docketNo)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => $this->recordType . ' record not found.']);
        }

        // Store the file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($this->folder, $fileName, 'local');

        // Get existing files
        $files = json_decode($record->files, true) ?? [];

        // Add new file
        $files[] = [
            'name' => $request->file_name,
            'path' => $path,
            'date_added' => now('Asia/Manila')->toDateTimeString(),
            'original_name' => $file->getClientOriginalName(),
        ];

        // Update the record
        $record->files = json_encode($files);
        $record->save();

        return response()->json(['success' => true, 'message' => 'File uploaded successfully.']);
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
