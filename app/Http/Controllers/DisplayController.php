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

        $totalRemDockets = $rem['total'];
        $totalHoaDockets = $hoa['total'];
        $totalDockets = $rem['total'] + $hoa['total'];
        $onShelf = $rem['onShelf'] + $hoa['onShelf'];
        $unavailable = $rem['unavailable'] + $hoa['unavailable'];
        $borrowed = $rem['borrowed'] + $hoa['borrowed'];

        return view('dashboard', compact(
            'totalDockets',
            'totalRemDockets',
            'totalHoaDockets',
            'onShelf',
            'unavailable',
            'borrowed'
        ));
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

        return view('rem', [
            'totalRemDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,   // pass to Blade
        ]);
    }

    // 🔹 HOA Dashboard
    // HOA Dashboard
    public function hoaDashboard()
    {
        $data = $this->getCounts(HoaDatabase::class);

        // Get unique province objects
        $provinces = HoaDatabase::with('province')
            ->get()
            ->pluck('province')
            ->unique('province_id')
            ->sortBy('province_name')
            ->values();

        return view('hoa', [
            'totalHoaDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,   // pass objects now
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
