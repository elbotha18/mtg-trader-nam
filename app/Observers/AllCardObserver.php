<?php

namespace App\Observers;

use App\Models\AllCard;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class AllCardObserver
{
    /**
     * Handle the AllCard "created" event.
     */
    public function created(AllCard $allCard): void
    {
        $this->logActivity($allCard, 'created', null, null, $allCard->toArray());
    }

    /**
     * Handle the AllCard "updated" event.
     */
    public function updated(AllCard $allCard): void
    {
        $changes = $allCard->getChanges();
        $original = $allCard->getOriginal();
        
        // Only log if there are actual changes
        if (!empty($changes)) {
            $oldValues = array_intersect_key($original, $changes);
            $this->logActivity($allCard, 'updated', null, $oldValues, $changes);
        }
    }

    /**
     * Handle the AllCard "deleted" event.
     */
    public function deleted(AllCard $allCard): void
    {
        $this->logActivity($allCard, 'deleted', null, $allCard->toArray(), null);
    }

    /**
     * Handle the AllCard "restored" event.
     */
    public function restored(AllCard $allCard): void
    {
        $this->logActivity($allCard, 'restored', null, null, $allCard->toArray());
    }

    /**
     * Handle the AllCard "force deleted" event.
     */
    public function forceDeleted(AllCard $allCard): void
    {
        $this->logActivity($allCard, 'force_deleted', null, $allCard->toArray(), null);
    }

    /**
     * Log the activity to the logs table
     */
    private function logActivity(AllCard $allCard, string $action, ?string $description = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        // Generate a description if not provided
        if (!$description) {
            $userName = Auth::user() ? Auth::user()->name : 'System';
            $description = "{$userName} {$action} card: {$allCard->name}";
        }

        Log::create([
            'user_id' => Auth::id(),
            'loggable_type' => AllCard::class,
            'loggable_id' => $allCard->id,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
