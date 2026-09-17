<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    /** Allowed MIME types for document upload */
    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /** Allowed document types */
    private const DOCUMENT_TYPES = [
        'ktp', 'npwp', 'ijazah', 'sertifikasi', 'kontrak', 'perjanjian_kerja',
        'foto', 'cv', 'pkwt', 'skck', 'lainnya',
    ];

    /**
     * List documents for an employee.
     */
    public function index(string $employeeId): JsonResponse
    {
        $this->authorize('employees.view');

        $employee = Employee::findOrFail($employeeId);

        $docs = EmployeeDocument::where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($doc) {
                $doc->download_url = Storage::temporaryUrl(
                    $doc->file_path,
                    now()->addMinutes(30)
                );
                return $doc;
            });

        return ApiResponse::success($docs);
    }

    /**
     * Upload a new document.
     * File stored privately (outside public root).
     */
    public function store(Request $request, string $employeeId): JsonResponse
    {
        $this->authorize('employees.manage');

        $employee = Employee::findOrFail($employeeId);

        $request->validate([
            'document_type' => ['required', 'string', 'in:' . implode(',', self::DOCUMENT_TYPES)],
            'title'         => ['required', 'string', 'max:255'],
            'file'          => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx'],
            'notes'         => ['nullable', 'string'],
        ]);

        $file = $request->file('file');

        // Validate MIME against allowed list
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            return ApiResponse::error(__('employee.document_invalid_mime'), 422);
        }

        // Store privately: employees/{tenant_id}/{employee_id}/documents/
        $path = $file->store(
            "employees/{$employee->tenant_id}/{$employee->id}/documents",
            'local'
        );

        $doc = EmployeeDocument::create([
            'employee_id'   => $employee->id,
            'document_type' => $request->document_type,
            'title'         => $request->title,
            'file_path'     => $path,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'notes'         => $request->notes,
            'uploaded_by'   => auth()->id(),
        ]);

        $doc->download_url = Storage::temporaryUrl($path, now()->addMinutes(30));

        return ApiResponse::created($doc, __('employee.document_uploaded'));
    }

    /**
     * Download document via signed URL (30 min expiry).
     */
    public function download(string $employeeId, string $documentId): JsonResponse
    {
        $this->authorize('employees.view');

        $doc = EmployeeDocument::where('employee_id', $employeeId)
            ->findOrFail($documentId);

        $url = Storage::temporaryUrl($doc->file_path, now()->addMinutes(30));

        return ApiResponse::success(['url' => $url, 'expires_at' => now()->addMinutes(30)->toISOString()]);
    }

    /**
     * Delete a document.
     */
    public function destroy(string $employeeId, string $documentId): JsonResponse
    {
        $this->authorize('employees.manage');

        $doc = EmployeeDocument::where('employee_id', $employeeId)
            ->findOrFail($documentId);

        Storage::delete($doc->file_path);
        $doc->delete();

        return ApiResponse::success(null, __('employee.document_deleted'));
    }
}
