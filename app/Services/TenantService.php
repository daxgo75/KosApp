<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantPhoto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class TenantService
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private LogService $logService,
    ) {}

    public function createTenant(array $data, array $photos = []): Tenant
    {
        return DB::transaction(function () use ($data, $photos) {
            $tenant = Tenant::create($data);

            if (!empty($photos)) {
                $this->uploadPhotos($tenant, $photos);
            }

            $this->logService->logAction('CREATE_TENANT', 'Tenant', $tenant->id, 'New tenant created');

            return $tenant->load('photos');
        });
    }

    public function updateTenant(Tenant $tenant, array $data, array $photos = []): Tenant
    {
        return DB::transaction(function () use ($tenant, $data, $photos) {
            $tenant->update($data);

            if (!empty($photos)) {
                $this->uploadPhotos($tenant, $photos);
            }

            $this->logService->logAction('UPDATE_TENANT', 'Tenant', $tenant->id);

            return $tenant->fresh(['photos']);
        });
    }

    public function uploadPhotos(Tenant $tenant, array $photos): Collection
    {
        $uploadedPhotos = collect();

        foreach ($photos as $photoData) {
            if ($photoData['file'] instanceof UploadedFile) {
                $file = $photoData['file'];
                $type = $photoData['type'] ?? 'profile';

                // Validate file
                $errors = $this->fileUploadService->validateFile($file, 'image');
                if (!empty($errors)) {
                    $this->logService->error('Invalid photo upload', ['errors' => $errors]);
                    continue;
                }

                // Store file
                $path = $this->fileUploadService->storeFile($file, "tenants/{$tenant->id}/{$type}");
                if (!$path) {
                    continue;
                }

                // Create record
                $photo = TenantPhoto::create([
                    'tenant_id' => $tenant->id,
                    'photo_type' => $type,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'status' => 'active',
                ]);

                $uploadedPhotos->push($photo);
            }
        }

        return $uploadedPhotos;
    }

    public function deletePhoto(TenantPhoto $photo): bool
    {
        return DB::transaction(function () use ($photo) {
            $this->fileUploadService->deleteFile($photo->file_path);
            return $photo->delete();
        });
    }

    public function activateTenant(Tenant $tenant): Tenant
    {
        $tenant->update(['status' => 'active']);
        return $tenant;
    }

    public function deactivateTenant(Tenant $tenant): Tenant
    {
        $tenant->update(['status' => 'inactive']);
        return $tenant;
    }

    public function suspendTenant(Tenant $tenant, string $reason = null): Tenant
    {
        $tenant->update([
            'status' => 'suspended',
            'notes' => $reason ? "Suspended: {$reason}" : $tenant->notes,
        ]);
        return $tenant;
    }
}
