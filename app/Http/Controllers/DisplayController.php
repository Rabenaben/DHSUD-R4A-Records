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

        $cards = [
            ['title' => 'Total Dockets', 'count' => $rem['total'] + $hoa['total'], 'from' => 'gray-600', 'to' => 'gray-900', 'text' => 'text-black', 'icon' => 'bi-folder2-open'],
            ['title' => 'Total REM Dockets', 'count' => $rem['total'], 'from' => 'blue-500', 'to' => 'blue-800', 'text' => 'text-black', 'icon' => 'bi-gear-wide-connected'],
            ['title' => 'Total HOA Dockets', 'count' => $hoa['total'], 'from' => 'orange-400', 'to' => 'orange-500', 'text' => 'text-black', 'icon' => 'bi-house-door-fill'],
            ['title' => 'On-Shelf', 'count' => $rem['onShelf'] + $hoa['onShelf'], 'from' => 'green-400', 'to' => 'green-700', 'text' => 'text-black', 'icon' => 'bi-archive-fill'],
            ['title' => 'Unavailable', 'count' => $rem['unavailable'] + $hoa['unavailable'], 'from' => 'red-500', 'to' => 'red-800', 'text' => 'text-black', 'icon' => 'bi-file-earmark-x-fill'],
            ['title' => 'Borrowed', 'count' => $rem['borrowed'] + $hoa['borrowed'], 'from' => 'yellow-300', 'to' => 'yellow-600', 'text' => 'text-black', 'icon' => 'bi-arrow-left-right'],
        ];

        return view('dashboard', compact('cards'));
    }

    // 🔹 REM Dashboard
    public function remDashboard()
    {
        $data = $this->getCounts(RemDatabase::class);

        // Get all unique provinces from REM table
        $provinces = RemDatabase::select('province')
            ->distinct()
            ->pluck('province')
            ->toArray();

        return view('rem_records.rem', [
            'totalRemDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,   // pass to Blade
        ]);
    }

    public function loadFolder($province)
    {
        $records = RemDatabase::where('province', $province)->get();
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

        // Get all HOA records with province and municipality relationships
        $hoaRecords = HoaDatabase::with(['province', 'municipality'])
            ->get();

        // Get unique province objects
        $provinces = HoaDatabase::with('province')
            ->get()
            ->pluck('province')
            ->unique('province_id')
            ->sortBy('province_name')
            ->values();

        // Get all municipalities with province relationship
        $municipalities = Municipality::with('province')
            ->orderBy('municipality_name')
            ->get();

        return view('hoa_records.hoa', [
            'totalHoaDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,   // pass objects now
            'municipalities' => $municipalities, // pass municipalities
            'hoaRecords' => $hoaRecords, // pass all HOA records
        ]);
    }

    // 🔹 Borrowers Dashboard
    public function borrowerDashboard()
    {
        $borrowers = Borrower::get();
        $nextId = Borrower::max('id') + 1;

        // Get unique docket numbers from HOA and REM databases
        $hoaDockets = HoaDatabase::pluck('docket_no')->unique()->sort()->values();
        $remDockets = RemDatabase::pluck('docket_no')->unique()->sort()->values();

        return view('borrowers.borrower', [
            'borrowers' => $borrowers,
            'nextId' => $nextId,
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
        ]);
    }


}
