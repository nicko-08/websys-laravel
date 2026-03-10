<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AuditLogResource extends JsonResource
{
    public function toArray($request): array
    {
        // Get a meaningful display name for the resource
        $resourceDisplay = $this->getResourceDisplay();

        return [
            'id' => $this->id,
            'action' => $this->action,
            'performed_by' => [
                'id' => $this->user_id,
                'name' => $this->user?->name,
                'role' => $this->user?->role,
            ],
            'resource' => class_basename($this->auditable_type),
            'resource_id' => $this->auditable_id,
            'resource_display' => $resourceDisplay, // ← Add this
            'details' => $this->details,
            'ip_address' => $this->ip_address,
            'performed_at' => $this->created_at->toIso8601String(),
        ];
    }

    /**
     * Get a human-readable display name for the audited resource
     */
    private function getResourceDisplay(): string
    {
        if (!$this->auditable) {
            return class_basename($this->auditable_type) . ' #' . $this->auditable_id;
        }

        $resource = $this->auditable;

        // Check for common display attributes
        if (isset($resource->name)) {
            return $resource->name;
        }

        if (isset($resource->title)) {
            return $resource->title;
        }

        if (isset($resource->email)) {
            return $resource->email;
        }

        if (isset($resource->description)) {
            return $resource->description;
        }

        // Fallback to class + ID
        return class_basename($this->auditable_type) . ' #' . $this->auditable_id;
    }
}
