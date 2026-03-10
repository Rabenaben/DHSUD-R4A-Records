<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientRequest;
use App\Models\HoaDatabase;
use App\Models\RemDatabase;

class ClientRequestController extends Controller
{
    use ActivityLoggingTrait;

    /**
     * Store a newly created client request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:HOA,REM',
            'project_name' => 'required|string|max:255',
            'docket_no' => 'required|string|regex:/^[A-Za-z0-9\-\_]+$/|max:50',
            'location' => 'nullable|string|max:255',
            'requested_by' => 'required|string|max:100',
            'or_no' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'requested_docs' => 'required|array|min:1|max:10',
            'others_specify' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'certified_true_copy' => 'nullable|string|in:certified,not_certified',
        ]);

        // Convert string value to boolean for storage
        $certifiedTrueCopy = $request->certified_true_copy === 'certified';

        $clientRequest = ClientRequest::create([
            'date' => $request->date,
            'type' => $request->type,
            'project_name' => $request->project_name,
            'docket_no' => $request->docket_no,
            'location' => $request->location,
            'requested_by' => $request->requested_by,
            'or_no' => $request->or_no,
            'amount' => $request->amount,
            'requested_docs' => $request->requested_docs,
            'others_specify' => $request->others_specify,
            'remarks' => $request->remarks,
            'certified_true_copy' => $certifiedTrueCopy,
        ]);

        // Log activity
        $this->logActivity($request->docket_no, null, 'Client Request', 'Added a client request');

        return response()->json([
            'success' => true,
            'message' => 'Client request added successfully.',
            'clientRequest' => $clientRequest,
        ]);
    }

    /**
     * Update an existing client request.
     */
    public function update(Request $request, $id)
    {
        $clientRequest = ClientRequest::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:HOA,REM',
            'project_name' => 'required|string|max:255',
            'docket_no' => 'required|string|regex:/^[A-Za-z0-9\-\_]+$/|max:50',
            'location' => 'nullable|string|max:255',
            'requested_by' => 'required|string|max:100',
            'or_no' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'requested_docs' => 'required|array|min:1|max:10',
            'others_specify' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'certified_true_copy' => 'nullable|boolean',
        ]);

        $oldDocketNo = $clientRequest->docket_no;

        $clientRequest->update([
            'date' => $request->date,
            'type' => $request->type,
            'project_name' => $request->project_name,
            'docket_no' => $request->docket_no,
            'location' => $request->location,
            'requested_by' => $request->requested_by,
            'or_no' => $request->or_no,
            'amount' => $request->amount,
            'requested_docs' => $request->requested_docs,
            'others_specify' => $request->others_specify,
            'remarks' => $request->remarks,
            'certified_true_copy' => $request->certified_true_copy ?? false,
        ]);

        // Log activity
        $this->logActivity($request->docket_no, $oldDocketNo, 'Client Request', 'Updated a client request');

        return response()->json([
            'success' => true,
            'message' => 'Client request updated successfully.',
            'clientRequest' => $clientRequest,
        ]);
    }

    /**
     * Search client requests by project name or docket number.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', '');

        $clientRequests = ClientRequest::when($query, function ($q) use ($query) {
            $q->where(function ($q2) use ($query) {
                $q2->where('project_name', 'like', "%{$query}%")
                    ->orWhere('docket_no', 'like', "%{$query}%");
            });
        })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Format dates and add requested_docs_array for view/edit mode
        $formattedRequests = $clientRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'date' => $request->date instanceof \Carbon\Carbon
                    ? $request->date->format('Y-m-d')
                    : $request->date,
                'type' => $request->type,
                'project_name' => $request->project_name,
                'docket_no' => $request->docket_no,
                'location' => $request->location,
                'requested_by' => $request->requested_by,
                'or_no' => $request->or_no,
                'amount' => $request->amount,
                'requested_docs' => $request->requested_docs,
                'others_specify' => $request->others_specify,
                'remarks' => $request->remarks,
                'certified_true_copy' => $request->certified_true_copy,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

        return response()->json($formattedRequests);
    }

/**
     * Get client requests data for display.
     */
    public function getData()
    {
        $clientRequests = ClientRequest::orderBy('created_at', 'desc')->get();

        // Format dates for each client request (Y-m-d for HTML date input)
        $formattedRequests = $clientRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'date' => $request->date instanceof \Carbon\Carbon
                    ? $request->date->format('Y-m-d')
                    : $request->date,
                'type' => $request->type,
                'project_name' => $request->project_name,
                'docket_no' => $request->docket_no,
                'location' => $request->location,
                'requested_by' => $request->requested_by,
                'or_no' => $request->or_no,
                'amount' => $request->amount,
                'requested_docs' => $request->requested_docs,
                'others_specify' => $request->others_specify,
                'remarks' => $request->remarks,
                'certified_true_copy' => $request->certified_true_copy,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

        // HOA Document Types
        $hoaDocumentTypes = [
            'Certificate of Incorporation',
            'Certificate of Amended By-Laws',
            'Certificate of Amended Articles of Incorporation',
            'Articles of Incorporation',
            'By-Laws',
            'Annual Report',
            'Election Report',
            'Masterlist',
            'General Information Sheet',
            'Others'
        ];

        // REM Document Types
        $remDocumentTypes = [
            'Certificate of Registration and License to Sell (CRLS)',
            'Notarized Fact Sheet / Sales Report',
            'Development Permit',
            'Verified Survey Returns (VSR)',
            'Subdivision Development Plan (SDP)',
            'Others'
        ];

        // Initialize stats arrays
        $hoaStats = [];
        $remStats = [];
        foreach ($hoaDocumentTypes as $doc) {
            $hoaStats[$doc] = 0;
        }
        foreach ($remDocumentTypes as $doc) {
            $remStats[$doc] = 0;
        }

        // Count occurrences of each document type, separated by HOA/REM
        foreach ($clientRequests as $request) {
            $requestedDocs = $request->requested_docs ?? [];
            $requestType = $request->type; // 'HOA' or 'REM'
            
            foreach ($requestedDocs as $doc) {
                if ($requestType === 'HOA' && isset($hoaStats[$doc])) {
                    $hoaStats[$doc]++;
                } elseif ($requestType === 'REM' && isset($remStats[$doc])) {
                    $remStats[$doc]++;
                }
            }
        }

        return response()->json([
            'clientRequests' => $formattedRequests,
            'hoaStats' => $hoaStats,
            'remStats' => $remStats,
        ]);
    }

    /**
     * Get docket numbers for dropdown based on type (HOA or REM).
     */
    public function getDocketNumbers(Request $request)
    {
        $type = $request->get('type', 'all');
        
        $dockets = [];
        
        if ($type === 'all' || $type === 'HOA') {
            // Get HOA docket numbers with their project names
            $hoaDockets = HoaDatabase::select('docket_no', 'hoa_name', 'location')
                ->whereNotNull('docket_no')
                ->where('docket_no', '!=', '')
                ->orderBy('docket_no')
                ->get()
                ->map(function ($item) {
                    return [
                        'docket_no' => $item->docket_no,
                        'project_name' => $item->hoa_name,
                        'location' => $item->location,
                        'type' => 'HOA'
                    ];
                });
            $dockets = array_merge($dockets, $hoaDockets->toArray());
        }
        
        if ($type === 'all' || $type === 'REM') {
            // Get REM docket numbers with their project names
            $remDockets = RemDatabase::select('docket_no', 'project_name', 'location')
                ->whereNotNull('docket_no')
                ->where('docket_no', '!=', '')
                ->orderBy('docket_no')
                ->get()
                ->map(function ($item) {
                    return [
                        'docket_no' => $item->docket_no,
                        'project_name' => $item->project_name,
                        'location' => $item->location,
                        'type' => 'REM'
                    ];
                });
            $dockets = array_merge($dockets, $remDockets->toArray());
        }
        
        // Sort by docket number
        usort($dockets, function ($a, $b) {
            return strcasecmp($a['docket_no'], $b['docket_no']);
        });
        
        return response()->json($dockets);
    }
}
