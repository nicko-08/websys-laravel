<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

final class LogModelModification
{
    private const CRITICAL_MODELS = ['Budget', 'User', 'Expense'];

    private const CRITICAL_ACTIONS = ['created', 'deleted'];

    public function handle(object $event): void
    {
        if (! $this->isValidEvent($event)) {
            return;
        }

        try {
            $this->storeAuditLog($event);

            if ($this->isCriticalAction($event->model, $event->action)) {
                $this->logCriticalAction($event);
            }
        } catch (Throwable $e) {
            Log::error('Audit logging failed', [
                'error' => $e->getMessage(),
                'user_id' => $event->user?->id,
                'action' => $event->action ?? null,
            ]);
        }
    }

    private function isValidEvent(object $event): bool
    {
        return isset($event->model, $event->user, $event->action)
            && $event->model instanceof Model;
    }

    private function storeAuditLog(object $event): void
    {
        $model = $event->model;

        AuditLog::create([
            'user_id' => $event->user->id,
            'action' => $event->action,
            'auditable_id' => $model->getKey(),
            'auditable_type' => $model::class,
            'details' => $this->getDetails($model, $event->action),
            'ip_address' => app()->runningInConsole()
                ? null
                : request()->ip(),
        ]);
    }

    private function sanitize(array $data, Model $model): array
    {
        $hidden = array_merge(
            $model->getHidden(),
            [
                'password',
                'remember_token',
                'api_token',
                'current_access_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
            ]
        );

        return array_diff_key(
            $data,
            array_flip($hidden)
        );
    }

    private function getDetails(Model $model, string $action): ?array
    {
        return match ($action) {
            'created' => [
                'attributes' => $this->sanitize($model->toArray(), $model),
            ],
            'updated' => [
                'before' => $this->sanitize($model->getOriginal(), $model),
                'after' => $this->sanitize($model->getChanges(), $model),
            ],
            'deleted' => [
                'attributes' => $this->sanitize($model->toArray(), $model),
            ],
            default => null,
        };
    }

    private function isCriticalAction(Model $model, string $action): bool
    {
        return in_array(class_basename($model), self::CRITICAL_MODELS, true)
            && in_array($action, self::CRITICAL_ACTIONS, true);
    }

    private function logCriticalAction(object $event): void
    {
        Log::info('Critical action performed', [
            'user_id' => $event->user->id,
            'action' => $event->action,
            'resource' => class_basename($event->model),
            'resource_id' => $event->model->getKey(),
        ]);
    }
}
