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

        // Get all unique provinces from REM table, excluding archived
        $provinces = RemDatabase::select('province')
            ->where('status', '!=', 'ARCHIVED')
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

        // Get paginated HOA records with province and municipality relationships
        $hoaRecords = HoaDatabase::with(['province', 'municipality'])
            ->paginate(10);

        // Get all provinces for the add record modal
        $provinces = \App\Models\Province::orderBy('province_name')->get();

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
            'hoaRecords' => $hoaRecords, // pass paginated HOA records
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

        // Attach status from borrower records to borrowers
        foreach ($borrowers as $borrower) {
            // Get all borrower records for this name
            $allBorrowerRecords = Borrower::where('borrower_name', $borrower->borrower_name)->get();

            // Check if any borrower record for this person has not been returned
            $hasUnreturnedRecords = $allBorrowerRecords->contains(function ($record) {
                return is_null($record->date_returned);
            });

            $borrower->status = $hasUnreturnedRecords ? 'Borrowed' : 'Returned';
        }

        return view('borrowers.borrower', [
            'borrowers' => $borrowers,
            'nextId' => $nextId,
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
        ]);
    }

    // 🔹 Load HOA Records for AJAX Pagination
    public function loadHoaRecordsAjax(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $province = $request->get('province', '');
        $municipality = $request->get('municipality', '');
        $region = $request->get('region', '');

        $query = HoaDatabase::with(['province', 'municipality']);

        // Apply filters
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('docket_no', 'like', '%' . $search . '%')
                  ->orWhere('hoa_name', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhereHas('province', function ($subQ) use ($search) {
                      $subQ->where('province_name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('municipality', function ($subQ) use ($search) {
                      $subQ->where('municipality_name', 'like', '%' . $search . '%');
                  });
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($province)) {
            $query->whereHas('province', function ($q) use ($province) {
                $q->where('province_name', $province);
            });
        }

        if (!empty($municipality)) {
            $query->whereHas('municipality', function ($q) use ($municipality) {
                $q->where('municipality_name', $municipality);
            });
        }

        if (!empty($region)) {
            $patterns = [
                'riv' => '%riv%',
                'str' => '%str%',
                'ncr hoa' => '%ncr%',
                'ncr hoa n' => '%ncr%',
                'r4a' => '%r4a%',
            ];
            if (isset($patterns[$region])) {
                $query->whereRaw('LOWER(docket_no) LIKE ?', [$patterns[$region]]);
            }
        }

        $hoaRecords = $query->paginate(10, ['*'], 'page', $page);

        // Set the correct path for pagination links
        $hoaRecords->setPath('/hoa_records');

        return response()->json([
            'table_html' => view('components.hoa.records-table', ['records' => $hoaRecords])->render(),
            'pagination_html' => $hoaRecords->links(),
            'current_page' => $hoaRecords->currentPage(),
            'last_page' => $hoaRecords->lastPage(),
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
