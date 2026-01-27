<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            $model->logAudit('created');
        });

        static::updated(function (Model $model) {
            $model->logAudit('updated');
        });

        static::deleted(function (Model $model) {
            $model->logAudit('deleted');
        });
    }

    public function logAudit(string $event)
    {
        $oldValues = [];
        $newValues = [];

        if ($event === 'updated') {
            $newValues = $this->getDirty();
            foreach (array_keys($newValues) as $attribute) {
                $oldValues[$attribute] = $this->getOriginal($attribute);
            }
        } elseif ($event === 'created') {
            $newValues = $this->getAttributes();
        } elseif ($event === 'deleted') {
            $oldValues = $this->getAttributes();
        }

        // Don't log if nothing changed (for updates)
        if ($event === 'updated' && empty($newValues)) {
            return;
        }

        // Exclude sensitive fields
        $exclude = ['password', 'remember_token'];
        $oldValues = array_diff_key($oldValues, array_flip($exclude));
        $newValues = array_diff_key($newValues, array_flip($exclude));

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
