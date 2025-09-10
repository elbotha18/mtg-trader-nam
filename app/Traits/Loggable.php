<?php

namespace App\Traits;

use App\Models\Log;

trait Loggable
{
    /**
     * Boot the loggable trait.
     */
    public static function bootLoggable()
    {
        static::created(function ($model) {
            $model->logActivity('created', 'Created ' . class_basename($model), null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            
            // Remove timestamps from changes for cleaner logging
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $model->logActivity('updated', 'Updated ' . class_basename($model), $original, $changes);
            }
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', 'Deleted ' . class_basename($model), $model->getAttributes());
        });
    }

    /**
     * Log an activity for this model.
     */
    public function logActivity($action, $description = null, $oldValues = null, $newValues = null, $metadata = null)
    {
        return Log::createLog($action, $this, $description, $oldValues, $newValues, $metadata);
    }

    /**
     * Get all logs for this model.
     */
    public function logs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
