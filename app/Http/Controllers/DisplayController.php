<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemDatabase;
use App\Models\HoaDatabase;
use App\Models\Municipality;
use App\Models\Borrower;

class DisplayController extends Controller
{
    // 🔹 Reusable function to count records for a given model
    private function getCounts($model)
    {
        return [
            'total' => $model::count(),
            'onShelf' => $model::where('status', 'ON-SHELF')->count(),
            'unavailable' => $model::where('status', 'UNAVAILABLE')->count(),
            'borrowed' => $model::where('status', 'BORROWED')->count(),
        ];
    }

    // 🔹 Combined dashboard
    public function index()
    {
        $rem = $this->getCounts(RemDatabase::class);
        $hoa = $this->getCounts(HoaDatabase::class);

        // Calculate total borrowed dockets from borrowers table (unique docket numbers not returned or with future return date)
        $borrowed = Borrower::where(function ($q) {
            $q->whereNull('date_returned')->orWhere('date_returned', '>', now());
        })->distinct('docket_number')->count('docket_number');

        $cards = [
            ['title' => 'Total Dockets', 'count' => $rem['total'] + $hoa['total'], 'from' => 'gray-600', 'to' => 'gray-900', 'text' => 'text-black', 'icon' => 'bi-folder2-open'],
            ['title' => 'Total REM Dockets', 'count' => $rem['total'], 'from' => 'blue-500', 'to' => 'blue-800', 'text' => 'text-black', 'icon' => 'bi-gear-wide-connected'],
            ['title' => 'Total HOA Dockets', 'count' => $hoa['total'], 'from' => 'orange-400', 'to' => 'orange-500', 'text' => 'text-black', 'icon' => 'bi-house-door-fill'],
            ['title' => 'On-Shelf', 'count' => $rem['onShelf'] + $hoa['onShelf'], 'from' => 'green-400', 'to' => 'green-700', 'text' => 'text-black', 'icon' => 'bi-archive-fill'],
            ['title' => 'Unavailable', 'count' => $rem['unavailable'] + $hoa['unavailable'], 'from' => 'red-500', 'to' => 'red-800', 'text' => 'text-black', 'icon' => 'bi-file-earmark-x-fill'],
            ['title' => 'Borrowed', 'count' => $borrowed, 'from' => 'yellow-300', 'to' => 'yellow-600', 'text' => 'text-black', 'icon' => 'bi-arrow-left-right'],
        ];

        return view('dashboard', compact('cards'));
    }

    // 🔹 REM Dashboard
    public function remDashboard()
    {
        $data = $this->getCounts(RemDatabase::class);

        // Get all unique provinces from REM table, excluding archived
        $provinces = RemDatabase::select('province')
            ->where('status', '!=', 'ARCHIVED')
            ->distinct()
            ->pluck('province')
            ->toArray();

        // Calculate borrowed based on borrowers table for REM dockets
        $remDockets = RemDatabase::pluck('docket_no')->toArray();
        $borrowed = Borrower::whereIn('docket_number', $remDockets)->whereNull('date_returned')->count();

        return view('rem_records.rem', [
            'totalRemDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $borrowed,
            'provinces' => $provinces,   // pass to Blade
        ]);
    }

    public function loadFolder($province)
    {
        $records = RemDatabase::where('province', $province)->where('status', '!=', 'ARCHIVED')->get();
        $provinceName = $province;

        return view('rem_records.partials.folder-table', [
            'records' => $records,
            'province' => $provinceName,
            'type' => 'REM'
        ]);
    }

    // 🔹 HOA Dashboard
    public function hoaDashboard()
    {
        $data = $this->getCounts(HoaDatabase::class);

        // Get all HOA records with province and municipality relationships, excluding archived
        $hoaRecords = HoaDatabase::with(['province', 'municipality'])
            ->where('status', '!=', 'ARCHIVED')
            ->get();

        // Get all provinces for the add record modal
        $provinces = \App\Models\Province::orderBy('province_name')->get();

        // Get all municipalities with province relationship
        $municipalities = Municipality::with('province')
            ->orderBy('municipality_name')
            ->get();

        // Calculate borrowed based on borrowers table for HOA dockets
        $hoaDockets = HoaDatabase::pluck('docket_no')->toArray();
        $borrowed = Borrower::whereIn('docket_number', $hoaDockets)->whereNull('date_returned')->count();

        return view('hoa_records.hoa', [
            'totalHoaDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $borrowed,
            'provinces' => $provinces,   // pass objects now
            'municipalities' => $municipalities, // pass municipalities
            'hoaRecords' => $hoaRecords, // pass all HOA records
        ]);
    }

    // 🔹 Borrowers Dashboard
    public function borrowerDashboard()
    {
        $borrowers = Borrower::orderBy('date_borrowed', 'desc')->get()->unique('borrower_name');
        $nextId = Borrower::max('id') + 1;

        // Get unique docket numbers from HOA and REM databases, excluding borrowed ones
        $hoaDockets = HoaDatabase::where('status', '!=', 'BORROWED')->pluck('docket_no')->unique()->sort()->values();
        $remDockets = RemDatabase::where('status', '!=', 'BORROWED')->pluck('docket_no')->unique()->sort()->values();

        // Attach status from docket tables to borrowers
        foreach ($borrowers as $borrower) {
            // Get all borrower records for this name
            $allBorrowerRecords = Borrower::where('borrower_name', $borrower->borrower_name)->get();

            $hasBorrowed = false;
            foreach ($allBorrowerRecords as $record) {
                if ($record->file_location === 'REM Records') {
                    $docket = RemDatabase::where('docket_no', $record->docket_number)->first();
                    if ($docket && $docket->status === 'BORROWED') {
                        $hasBorrowed = true;
                        break;
                    }
                } elseif ($record->file_location === 'HOA Records') {
                    $docket = HoaDatabase::where('docket_no', $record->docket_number)->first();
                    if ($docket && $docket->status === 'BORROWED') {
                        $hasBorrowed = true;
                        break;
                    }
                }
            }

            $borrower->status = $hasBorrowed ? 'Borrowed' : 'Returned';
        }

        return view('borrowers.borrower', [
            'borrowers' => $borrowers,
            'nextId' => $nextId,
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
        ]);
    }

    // 🔹 Archived Files Dashboard
    public function archivedDashboard()
    {
        $archivedFiles = [];

        // Collect archived files from HOA records
        $hoaRecords = HoaDatabase::all();
        foreach ($hoaRecords as $record) {
            $files = json_decode($record->files, true) ?? [];
            foreach ($files as $index => $file) {
                if (isset($file['archived']) && $file['archived']) {
                    $archivedFiles[] = [
                        'type' => 'hoa',
                        'docket_no' => $record->docket_no,
                        'record_name' => $record->hoa_name,
                        'file_name' => $file['name'] ?? 'Unknown',
                        'file_index' => $index,
                        'date_added' => $file['date_added'] ?? null,
                        'last_updated_by' => $file['last_updated_by'] ?? null,
                    ];
                }
            }
        }

        // Collect archived files from REM records
        $remRecords = RemDatabase::all();
        foreach ($remRecords as $record) {
            $files = json_decode($record->files, true) ?? [];
            foreach ($files as $index => $file) {
                if (isset($file['archived']) && $file['archived']) {
                    $archivedFiles[] = [
                        'type' => 'rem',
                        'docket_no' => $record->docket_no,
                        'record_name' => $record->project_name,
                        'file_name' => $file['name'] ?? 'Unknown',
                        'file_index' => $index,
                        'date_added' => $file['date_added'] ?? null,
                        'last_updated_by' => $file['last_updated_by'] ?? null,
                    ];
                }
            }
        }

        return view('archived.archive', compact('archivedFiles'));
    }
}
