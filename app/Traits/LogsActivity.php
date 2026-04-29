<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function (self $model) use ($event) {
                ActivityLog::create([
                    'user_id'      => auth()->id(),
                    'subject_type' => static::class,
                    'subject_id'   => $model->getKey(),
                    'event'        => $event,
                    'description'  => class_basename(static::class) . ' ' . $event,
                    'ip_address'   => request()->ip(),
                    'user_agent'   => substr(request()->userAgent() ?? '', 0, 255),
                ]);
            });
        }
    }
}
