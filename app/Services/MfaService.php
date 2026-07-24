<?php

namespace App\Services;

use App\Models\User;
use App\Models\MfaCode;
use App\Notifications\MfaCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MfaService
{
    protected int $validForMinutes = 10;
    protected int $maxAttempts = 5;

    //gen code
    public function generateAndSend(User $user): void
    {
        // On invalide les anciens codes non utilisés pour cet utilisateur
        MfaCode::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->delete();

        $code = (string) random_int(100000, 999999);

        MfaCode::create([
            'user_id'    => $user->id,
            'code'       => Hash::make($code),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes($this->validForMinutes),
        ]);

        $user->notify(new MfaCodeNotification($code, $this->validForMinutes));
    }

    //check du code
    public function verify(User $user, string $code): array
    {
        $mfaCode = MfaCode::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$mfaCode) {
            return ['success' => false, 'message' => "Aucun code actif. Demandez-en un nouveau."];
        }

        if ($mfaCode->isExpired()) {
            return ['success' => false, 'message' => 'Ce code a expiré. Demandez-en un nouveau.'];
        }

        if ($mfaCode->attempts >= $this->maxAttempts) {
            return ['success' => false, 'message' => "Trop de tentatives. Demandez un nouveau code."];
        }

        if (!Hash::check($code, $mfaCode->code)) {
            $mfaCode->increment('attempts');
            $remaining = $this->maxAttempts - $mfaCode->attempts;
            return ['success' => false, 'message' => "Code incorrect. {$remaining} tentative(s) restante(s)."];
        }

        $mfaCode->update(['consumed_at' => now()]);

        return ['success' => true, 'message' => 'Code vérifié.'];
    }

    // Un code actif (non expiré, non consommé) existe-t-il déjà pour cet utilisateur ?
    public function hasActiveCode(User $user): bool
    {
        return MfaCode::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->exists();
    }
}
