<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function viewAsProvider(User $user, Quote $quote): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role !== 'provider') return false;

        $providerId = optional($user->provider)->id;
        return $providerId && (int)$quote->provider_id === (int)$providerId;
    }

    public function updateStatusAsProvider(User $user, Quote $quote): bool
    {
        // Même règle que view
        return $this->viewAsProvider($user, $quote);
    }
}
