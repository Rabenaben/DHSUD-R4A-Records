<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemDatabase;
use App\Models\HoaDatabase;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Borrower;
use App\Models\ClientRequest;

class DisplayController extends Controller
{
    // 🔹 Reusable function to count records for a given model
    private function getCounts($model)
    {
        // Use single query with conditional aggregation for better performance
        $counts = $model::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'ON-SHELF' THEN 1 ELSE 0 END) as onShelf,
            SUM(CASE WHEN status = 'UNAVAILABLE' THEN 1 ELSE 0 END) as unavailable,
            SUM(CASE WHEN status = 'BORROWED' THEN 1 ELSE 0 END) as borrowed
        ")->first();

        return [
            'total' => $counts->total ?? 0,
            'onShelf' => $counts->onShelf ?? 0,
            'unavailable' => $counts->unavailable ?? 0,
            'borrowed' => $counts->borrowed ?? 0,
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

        // Get all unique provinces from REM table with province relationship (like HOA)
        $remRecords = RemDatabase::with('province')
            ->where('status', '!=', 'ARCHIVED')
            ->get();

        // Get unique provinces with their IDs
        $provinces = $remRecords->map(function ($record) {
            return $record->province;
        })->filter()->unique('province_id')->values();

        // Get all provinces for the add record modal
        $allProvinces = Province::orderBy('province_name')->get();

        // Get all municipalities with province relationship
        $municipalities = Municipality::with('province')
            ->orderBy('municipality_name')
            ->get();

        return view('rem_records.rem', [
            'totalRemDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,
            'allProvinces' => $allProvinces,
            'municipalities' => $municipalities,
        ]);
    }

    public function loadFolder($provinceId)
    {
        $records = RemDatabase::with(['province', 'municipality'])
            ->where('province_id', $provinceId)
            ->get();

        // Get province name from the first record or lookup
        $province = Province::find($provinceId);
        $provinceName = $province ? $province->province_name : 'Unknown';

        return view('rem_records.partials.folder-table', [
            'records' => $records,
            'province' => $provinceName,
            'provinceId' => $provinceId,
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
        $provinces = Province::orderBy('province_name')->get();

        // Get all municipalities with province relationship
        $municipalities = Municipality::with('province')
            ->orderBy('municipality_name')
            ->get();

        return view('hoa_records.hoa', [
            'totalHoaDockets' => $data['total'],
            'onShelf' => $data['onShelf'],
            'unavailable' => $data['unavailable'],
            'borrowed' => $data['borrowed'],
            'provinces' => $provinces,
            'municipalities' => $municipalities,
            'hoaRecords' => $hoaRecords,
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
            if ($region === 'ncr hoa') {
                // Match "ncr-hoa" or "ncr hoa" at the BEGINNING followed by:
                // - End of string (exact match like "ncr hoa")
                // - A space/hyphen followed by digits (like "ncr hoa 1", "ncr-hoa-2")
                // But NOT "ncr-hoa-n" or "ncr hoa n" (the "n" variant)
                $query->whereRaw("LOWER(docket_no) REGEXP '^ncr[- ]hoa($|[- ][0-9])' AND LOWER(docket_no) NOT REGEXP '^ncr[- ]hoa[- ]n'");
            } elseif ($region === 'ncr hoa n') {
                // Match "ncr-hoa-n", "ncr hoa n", "ncr-hoa n", or "ncr hoa-n" at the beginning
                $query->whereRaw("LOWER(docket_no) REGEXP '^ncr[- ]hoa[- ]n'");
            } else {
                $patterns = [
                    'riv' => '%riv%',
                    'str' => '%str%',
                    'r4a' => '%r4a%',
                ];
                if (isset($patterns[$region])) {
                    $query->whereRaw('LOWER(docket_no) LIKE ?', [$patterns[$region]]);
                }
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

    // 🔹 Borrowers Dashboard
    public function borrowerDashboard()
    {
        // Get all borrowers ordered by date, then group by borrower_name to get the most recent record for each
        $allBorrowers = Borrower::orderBy('date_borrowed', 'desc')->get();

        // Group by borrower_name and get the first record (most recent) for each
        $borrowers = $allBorrowers->groupBy('borrower_name')->map(function ($group) {
            return $group->first();
        })->values();

        // Get next ID from auto-increment
        $nextId = Borrower::max('id') + 1;

        // Get unique docket numbers from HOA and REM databases, excluding borrowed ones
        $hoaDockets = HoaDatabase::where('status', '!=', 'BORROWED')->pluck('docket_no')->unique()->sort()->values();
        $remDockets = RemDatabase::where('status', '!=', 'BORROWED')->pluck('docket_no')->unique()->sort()->values();

        // Divisions array
        $divisions = ['HREDRD - PRLS', 'HREDRD - EMES', 'RECORDS', 'HOACDD', 'ELUUPDD', 'PHSD'];

        // Pre-fetch all borrower records to avoid N+1 query
        $allBorrowerNames = $borrowers->pluck('borrower_name')->toArray();
        $allBorrowerRecords = Borrower::whereIn('borrower_name', $allBorrowerNames)->get()->groupBy('borrower_name');

        // Attach status from borrower records to borrowers
        foreach ($borrowers as $borrower) {
            $borrowerRecords = $allBorrowerRecords[$borrower->borrower_name] ?? collect();

            // Check if any borrower record for this person has not been returned
            $hasUnreturnedRecords = $borrowerRecords->contains(function ($record) {
                return is_null($record->date_returned);
            });

            $borrower->status = $hasUnreturnedRecords ? 'Borrowed' : 'Returned';
        }

        return view('borrowers.borrower', [
            'borrowers' => $borrowers,
            'nextId' => $nextId,
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
            'divisions' => $divisions,
        ]);
    }

    // 🔹 Request History Dashboard
    public function requestHistoryDashboard()
    {
        $clientRequests = ClientRequest::orderBy('created_at', 'desc')->get();

        // Calculate stats for each document type
        $documentTypes = [
            'Certificate of Incorporation',
            'Certificate of Amended By-Laws',
            'Certificate of Amended Articles of Incorporation',
            'Articles of Incorporation',
            'By-Laws',
            'Annual Report',
            'Election Report'
        ];

        // Initialize stats array
        $docStats = [];
        foreach ($documentTypes as $doc) {
            $docStats[$doc] = 0;
        }

        // Count occurrences of each document type
        foreach ($clientRequests as $request) {
            $requestedDocs = $request->requested_docs_array;
            foreach ($requestedDocs as $doc) {
                if (isset($docStats[$doc])) {
                    $docStats[$doc]++;
                }
            }
        }

        return view('request-history.request-history', compact('clientRequests', 'docStats'));
    }

    // 🔹 Archived Files Dashboard
    public function archivedDashboard()
    {
        $archivedFiles = [];

        // Collect archived files from HOA records - use chunking for memory efficiency
        HoaDatabase::select('docket_no', 'hoa_name', 'files')
            ->whereNotNull('files')
            ->where('files', '!=', '[]')
            ->chunk(100, function ($records) use (&$archivedFiles) {
                foreach ($records as $record) {
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
            });

        // Collect archived files from REM records - use chunking for memory efficiency
        RemDatabase::select('docket_no', 'project_name', 'files')
            ->whereNotNull('files')
            ->where('files', '!=', '[]')
            ->chunk(100, function ($records) use (&$archivedFiles) {
                foreach ($records as $record) {
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
            });

        return view('archived.archive', compact('archivedFiles'));
    }
}
