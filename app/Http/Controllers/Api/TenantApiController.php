<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Tenant::class);

        $tenants = Tenant::query()
            ->with(['room', 'payments'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->paginate(15);

        LogService::logAction('LIST_TENANTS', 'Tenant');

        return $this->paginated($tenants, 'Tenants retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Tenant::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'id_type' => 'required|in:ktp,sim,passport',
            'id_number' => 'required|string|unique:tenants',
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        try {
            $tenant = Tenant::create($validated);
            LogService::logAction('CREATE_TENANT', 'Tenant', $tenant->id);

            return $this->success($tenant, 'Tenant created successfully', 201);
        } catch (\Exception $e) {
            LogService::error('Failed to create tenant', ['error' => $e->getMessage()]);
            return $this->error('Failed to create tenant', 500);
        }
    }

    public function show(Tenant $tenant): JsonResponse
    {
        $this->authorize('view', $tenant);

        LogService::logAction('VIEW_TENANT', 'Tenant', $tenant->id);

        return $this->success($tenant->load(['room', 'payments', 'photos']), 'Tenant retrieved successfully');
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $this->authorize('update', $tenant);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => "email|unique:tenants,email,{$tenant->id}",
            'phone' => 'string|max:20',
            'address' => 'string',
            'city' => 'string|max:100',
            'province' => 'string|max:100',
            'postal_code' => 'string|max:10',
            'status' => 'in:active,inactive,suspended',
        ]);

        try {
            $tenant->update($validated);
            LogService::logAction('UPDATE_TENANT', 'Tenant', $tenant->id);

            return $this->success($tenant, 'Tenant updated successfully');
        } catch (\Exception $e) {
            LogService::error('Failed to update tenant', ['error' => $e->getMessage()]);
            return $this->error('Failed to update tenant', 500);
        }
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $this->authorize('delete', $tenant);

        try {
            $tenant->delete();
            LogService::logAction('DELETE_TENANT', 'Tenant', $tenant->id);

            return $this->success(null, 'Tenant deleted successfully');
        } catch (\Exception $e) {
            LogService::error('Failed to delete tenant', ['error' => $e->getMessage()]);
            return $this->error('Failed to delete tenant', 500);
        }
    }
}
