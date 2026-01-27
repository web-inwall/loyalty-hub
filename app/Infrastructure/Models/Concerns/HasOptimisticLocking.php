<?php

declare(strict_types=1);

namespace App\Infrastructure\Models\Concerns;

use App\Infrastructure\Exceptions\StaleModelLockingException;
use Illuminate\Database\Eloquent\Builder;

trait HasOptimisticLocking
{
    /**
     * Boot the trait.
     */
    public static function bootHasOptimisticLocking(): void
    {
        static::updating(function ($model) {
            // Increment version before saving
            $model->version++;
        });
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query = parent::setKeysForSaveQuery($query);

        // Add version check to the query
        if (isset($this->original['version'])) {
            $query->where('version', $this->original['version']);
        }

        return $query;
    }

    /**
     * Perform the actual update operation on a model instance.
     *
     * @return mixed
     */
    protected function performUpdate(Builder $query)
    {
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        // Apply keys (ID + Version) to the query
        $query = $this->setKeysForSaveQuery($query);

        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            $affected = $query->update($dirty);

            if ($affected === 0) {
                // Check if the record actually exists (it might have been deleted, or version mismatch)
                $exists = $this->newQueryWithoutScopes()->whereKey($this->getKey())->exists();

                if ($exists) {
                    // If it exists but wasn't updated, it's a version mismatch
                    throw new StaleModelLockingException('The model has been modified by another process.');
                }
            }

            $this->syncChanges();

            $this->fireModelEvent('updated', false);
        }

        return true;
    }
}
