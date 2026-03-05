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
            'remarks' => 'nullable|string|max:1000',
        ]);

        $clientRequest = ClientRequest::create([
            'date' => $request->date,
            'type' => $request->type,
            'project_name' => $request->project_name,
            'docket_no' => $request->docket_no,
            'location' => $request->location,
            'requested_by' => $request->requested_by,
            'or_no' => $request->or_no,
            'amount' => $request->amount,
            'requested_docs' => json_encode($request->requested_docs),
            'remarks' => $request->remarks,
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
            'remarks' => 'nullable|string|max:1000',
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
            'requested_docs' => json_encode($request->requested_docs),
            'remarks' => $request->remarks,
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
            $q->where('project_name', 'like', "%{$query}%")
                ->orWhere('docket_no', 'like', "%{$query}%");
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
                'requested_docs_array' => $request->requested_docs_array,
                'remarks' => $request->remarks,
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
                'requested_docs_array' => $request->requested_docs_array,
                'remarks' => $request->remarks,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

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

        return response()->json([
            'clientRequests' => $formattedRequests,
            'docStats' => $docStats,
        ]);
    }
}
