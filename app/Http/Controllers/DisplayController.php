<?php

namespace App\Http\Controllers;

use App\Models\RemDatabase;
use App\Models\HoaDatabase;

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
            ['title' => 'Total Dockets', 'count' => $rem['total'] + $hoa['total'], 'from' => 'gray-600', 'to' => 'gray-900', 'text' => 'text-white', 'icon' => 'bi-folder2-open'],
            ['title' => 'Total REM Dockets', 'count' => $rem['total'], 'from' => 'blue-500', 'to' => 'blue-800', 'text' => 'text-white', 'icon' => 'bi-gear-wide-connected'],
            ['title' => 'Total HOA Dockets', 'count' => $hoa['total'], 'from' => 'orange-400', 'to' => 'orange-700', 'text' => 'text-white', 'icon' => 'bi-house-door-fill'],
            ['title' => 'On-Shelf', 'count' => $rem['onShelf'] + $hoa['onShelf'], 'from' => 'green-400', 'to' => 'green-700', 'text' => 'text-white', 'icon' => 'bi-archive-fill'],
            ['title' => 'Unavailable', 'count' => $rem['unavailable'] + $hoa['unavailable'], 'from' => 'red-500', 'to' => 'red-800', 'text' => 'text-black', 'icon' => 'bi-file-earmark-x-fill'],
            ['title' => 'Borrowed', 'count' => $rem['borrowed'] + $hoa['borrowed'], 'from' => 'yellow-300', 'to' => 'yellow-600', 'text' => 'text-white', 'icon' => 'bi-arrow-left-right'],
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

        return view('rem_records', [
            'totalRemDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,   // pass to Blade
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

        return view('hoa_records', [
            'totalHoaDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,   // pass objects now
            'hoaRecords' => $hoaRecords, // pass all HOA records
        ]);
    }

    public function loadFolder($theme, $province)
    {
        if ($theme === 'rem') {
            $records = RemDatabase::where('province', $province)->get();
            $provinceName = $province;
        } elseif ($theme === 'hoa') {
            // Fetch HOA records with province & municipality
            $records = HoaDatabase::with(['province', 'municipality'])
                ->where('province_id', $province)
                ->get();

            // If there are records, get province name from first record
            if ($records->isNotEmpty()) {
                $provinceName = $records->first()->province->province_name ?? 'Unknown';
            } else {
                // If no records, fetch province name from Provinces table directly
                $provinceRecord = \App\Models\Province::find($province);
                $provinceName = $provinceRecord->province_name ?? 'Unknown';
            }
        } else {
            abort(404);
        }

        return view('partials.folder-table', [
            'records' => $records,
            'province' => $provinceName,
            'type' => strtoupper($theme)
        ]);
    }
}
