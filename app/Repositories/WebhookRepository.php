<?php
namespace App\Repositories;
use Illuminate\Support\Facades\Auth;

use App\Models\WebhookLog;

class WebhookRepository
{
    //create webhook log
    public function create(array $data)
    {
        return WebhookLog::create($data);
    }
    //check si un event extern est deja traite
    public function exists(string $provider, string $externalEventId): bool
    {
        return WebhookLog::where('provider', $provider)
                           ->where('external_event_id', $externalEventId)
                           ->exists();
    }
    //mark as processed
    public function markProcessed(string $externalEventId): bool
    {
        $log = WebhookLog::where('external_event_id', $externalEventId)->first();
        if ($log) {
            $log->processed = true;
            $log->processed_at = now();
            return $log->save();
        }
        return false;
    }
    

}