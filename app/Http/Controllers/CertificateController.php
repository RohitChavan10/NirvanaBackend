<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
     /**
     * 🔹 Get all certificates
     * GET /certificates
     */
    public function all()
    {
        $certificates = Certificate::with('building')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $certificates
        ], 200);
    }

    /**
     * 🔹 Store certificate(s) with file upload
     * POST /certificates
     */
    public function store(Request $request, $building_id)
    {
        $request->validate([
            'building_id' => $building_id,
            'certificate_number' => 'nullable|string',
            'certificate_name'   => 'required|string',
            'certificate_type'   => 'nullable|string',
            'owner_name'         => 'nullable|string',
            'owner_address'      => 'nullable|string',
            'issued_by'          => 'nullable|string',
            'approved_by'        => 'nullable|string',
            'issued_date'        => 'nullable|date',
            'expiry_date'        => 'nullable|date',
            'status'             => 'nullable|string',
            'notes'              => 'nullable|string',
            'files'              => 'required',
            'files.*'            => 'file|mimes:pdf,jpg,jpeg,png,svg,doc,docx,txt|max:10240',
        ]);

        $uploaded = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {

                $filename = time() . '_' . $file->getClientOriginalName();

                // Store file
                $path = $file->storeAs('certificates', $filename, 'public');

                // FULL URL
                $fullUrl = config('app.url') . '/storage/' . $path;

                $certificate = Certificate::create([
                    'building_id'        => $request->building_id,
                    'certificate_number' => $request->certificate_number,
                    'certificate_name'   => $request->certificate_name,
                    'certificate_type'   => $request->certificate_type,
                    'owner_name'         => $request->owner_name,
                    'owner_address'      => $request->owner_address,
                    'issued_by'          => $request->issued_by,
                    'approved_by'        => $request->approved_by,
                    'issued_date'        => $request->issued_date,
                    'expiry_date'        => $request->expiry_date,
                    'status'             => $request->status ?? 'pending',
                    'file_path'          => $fullUrl,
                    'notes'              => $request->notes,
                ]);

                $uploaded[] = $certificate;
            }
        }

        return response()->json([
            'message' => 'Certificates uploaded successfully',
            'data'    => $uploaded
        ], 201);
    }

    /**
     * 🔹 Get certificates by building
     * GET /buildings/{building_id}/certificates
     */
    public function byBuilding($building_id)
    {
        $certificates = Certificate::where('building_id', $building_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'building_id' => $building_id,
            'data' => $certificates
        ], 200);
    }

    /**
     * 🔹 Show single certificate
     * GET /certificates/{id}
     */
    public function show($id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        return response()->json([
            'data' => $certificate
        ], 200);
    }

    /**
     * 🔹 Update certificate metadata
     * PUT /certificates/{id}
     */
    public function update(Request $request, $id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        $request->validate([
            'certificate_number' => 'nullable|string',
            'certificate_name'   => 'nullable|string',
            'certificate_type'   => 'nullable|string',
            'owner_name'         => 'nullable|string',
            'owner_address'      => 'nullable|string',
            'issued_by'          => 'nullable|string',
            'approved_by'        => 'nullable|string',
            'issued_date'        => 'nullable|date',
            'expiry_date'        => 'nullable|date',
            'status'             => 'nullable|string',
            'notes'              => 'nullable|string',
        ]);

        $certificate->update($request->only([
            'certificate_number',
            'certificate_name',
            'certificate_type',
            'owner_name',
            'owner_address',
            'issued_by',
            'approved_by',
            'issued_date',
            'expiry_date',
            'status',
            'notes',
        ]));

        return response()->json([
            'message' => 'Certificate updated successfully',
            'data' => $certificate
        ], 200);
    }

    /**
     * 🔹 Delete certificate
     * DELETE /certificates/{id}
     */
    public function destroy($id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        // Optional: delete file
        if ($certificate->file_path) {
            $relativePath = str_replace(config('app.url') . '/storage/', '', $certificate->file_path);
            Storage::disk('public')->delete($relativePath);
        }

        $certificate->delete();

        return response()->json([
            'message' => 'Certificate deleted successfully'
        ], 200);
    }
}