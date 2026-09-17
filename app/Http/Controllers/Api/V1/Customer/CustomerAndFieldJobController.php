<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FieldJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CustomerAndFieldJobController
 *
 * Mengelola data klien/pelanggan dan eksekusi pekerjaan lapangan (Field Jobs):
 * - CRUD Customer
 * - Riwayat Field Jobs per customer
 * - Penugasan Field Job ke teknisi/sales
 * - GPS Check-in & penyelesaian pekerjaan lapangan
 */
class CustomerAndFieldJobController extends Controller
{
    // ==========================================
    // Customer Endpoints
    // ==========================================

    public function indexCustomers(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $customers = Customer::where('tenant_id', $tenantId)
            ->with(['assignedUser'])
            ->withCount('fieldJobs')
            ->when($request->filled('search'), fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($customers);
    }

    public function storeCustomer(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'assigned_user_id' => 'nullable|uuid|exists:users,id',
            'notes'            => 'nullable|string',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['created_by'] = $request->user()->id;

        $customer = Customer::create($validated);

        return ApiResponse::created($customer, 'Data pelanggan berhasil disimpan');
    }

    public function showCustomer(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $customer = Customer::where('tenant_id', $tenantId)
            ->with(['assignedUser', 'fieldJobs.assignee.user'])
            ->findOrFail($id);

        return ApiResponse::success($customer, 'Detail pelanggan berhasil diambil');
    }

    public function getCustomerJobs(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $customer = Customer::where('tenant_id', $tenantId)->findOrFail($id);

        $jobs = $customer->fieldJobs()
            ->with('assignee.user')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($jobs);
    }

    // ==========================================
    // Field Job Endpoints
    // ==========================================

    public function indexJobs(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $jobs = FieldJob::where('tenant_id', $tenantId)
            ->with(['customer', 'assignee.user', 'project'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('assigned_to'), fn($q) => $q->where('assigned_to', $request->assigned_to))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($jobs);
    }

    public function storeJob(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'customer_id'    => 'nullable|uuid|exists:customers,id',
            'project_id'     => 'nullable|uuid|exists:projects,id',
            'assigned_to'    => 'required|uuid|exists:employees,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'site_name'      => 'nullable|string|max:255',
            'site_address'   => 'nullable|string',
            'site_latitude'  => 'nullable|numeric|between:-90,90',
            'site_longitude' => 'nullable|numeric|between:-180,180',
            'scheduled_at'   => 'required|date',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['assigned_by'] = $request->user()->id;
        $validated['status'] = 'assigned';

        $job = FieldJob::create($validated);
        $job->load(['customer', 'assignee.user']);

        return ApiResponse::created($job, 'Pekerjaan lapangan berhasil dijadwalkan');
    }

    public function checkInJob(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $job = FieldJob::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $job->update([
            'checked_in_at'      => now(),
            'check_in_latitude'  => $validated['latitude'],
            'check_in_longitude' => $validated['longitude'],
            'status'             => 'in_progress',
        ]);

        return ApiResponse::success($job, 'Check-in lokasi pekerjaan lapangan berhasil');
    }

    public function completeJob(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $job = FieldJob::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'work_notes'         => 'required|string|max:2000',
            'customer_signature' => 'nullable|string',
        ]);

        $job->update([
            'completed_at'       => now(),
            'status'             => 'completed',
            'work_notes'         => $validated['work_notes'],
            'customer_signature' => $validated['customer_signature'] ?? null,
        ]);

        return ApiResponse::success($job, 'Pekerjaan lapangan berhasil diselesaikan');
    }
}
