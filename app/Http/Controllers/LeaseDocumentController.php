<?php

namespace App\Http\Controllers;

use App\Models\LeaseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaseDocumentController extends Controller
{
/**
     * 🔹 Get ALL documents (for /documents page)
     */
    public function all()
    {
        $documents = LeaseDocument::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $documents
        ], 200);
    }

    /**
     * 🔹 Upload one or multiple documents
     * POST /documents
     */
    public function store(Request $request)
    {
       $request->validate([
    'lease_id'   => 'required|exists:leases,id',
    'documents' => 'required',
    'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
    'file_type' => 'nullable|string',
]);

        $uploadedDocs = [];

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {

                $filename = time() . '_' . $file->getClientOriginalName();

                // Store in: storage/app/public/lease_documents
                $path = $file->storeAs('lease_docs', $filename, 'public');

                $doc = LeaseDocument::create([
                    'lease_id'     => $request->lease_id,
                    'file_name'    => $file->getClientOriginalName(),
                    'file_path'    => $path, // IMPORTANT: only relative path
                    'file_type' => $request->file_type,
                    'uploaded_by'  => $request->user()->user_id ?? null,
                ]);

                $uploadedDocs[] = $doc;
            }
        }

        return response()->json([
            'message' => 'Documents uploaded successfully',
            'data' => $uploadedDocs
        ], 201);
    }

    /**
     * 🔹 Get documents by Lease
     * GET /leases/{lease_id}/documents
     */
    public function index($lease_id)
    {
        $documents = LeaseDocument::where('lease_id', $lease_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'lease_id' => $lease_id,
            'data' => $documents
        ], 200);
    }

    /**
     * 🔹 Show single document by ID
     * GET /documents/{id}
     */
    public function show($id)
    {
        $document = LeaseDocument::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        return response()->json([
            'data' => $document
        ], 200);
    }

    /**
     * 🔹 Update document metadata (rename, type)
     * PUT /documents/{id}
     */
    public function update(Request $request, $id)
    {
        $document = LeaseDocument::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $request->validate([
            'file_name'     => 'nullable|string',
            'file_type'=> 'nullable|string',
        ]);

        if ($request->has('file_name')) {
            $document->file_name = $request->file_name;
        }

        if ($request->has('file_type')) {
            $document->file_type = $request->file_type;
        }

        $document->save();

        return response()->json([
            'message' => 'Document updated successfully',
            'data' => $document
        ], 200);
    }
}
