<?php

namespace App\Services\Loyalty;

use App\Enums\LoyaltyActivityType;
use App\Enums\LoyaltyProgramType;
use App\Models\Business;
use App\Models\LoyaltyActivity;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyReward;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Lógica central del módulo de fidelización: programas, clientes, puntos,
 * visitas, sellos, recompensas e historial de actividades.
 */
class LoyaltyService
{
    public function program(Business $business): LoyaltyProgram
    {
        return $business->loyaltyProgram()->firstOrCreate([], [
            'name' => 'Club '.$business->name,
            'description' => 'Acumula y obtén recompensas por tu preferencia.',
            'type' => LoyaltyProgramType::Points,
            'unit_name' => 'puntos',
            'earn_amount' => 1000,
            'earn_units' => 10,
            'is_active' => true,
        ]);
    }

    public function nextCode(Business $business): string
    {
        $number = LoyaltyMember::ofBusiness($business)->count() + 1;

        do {
            $code = 'CLI-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
            $number++;
        } while (LoyaltyMember::ofBusiness($business)->where('code', $code)->exists());

        return $code;
    }

    public function createMember(Business $business, array $data): LoyaltyMember
    {
        return DB::transaction(function () use ($business, $data) {
            return LoyaltyMember::create([
                'business_id' => $business->id,
                'code' => $this->nextCode($business),
                'token' => $this->uniqueToken(),
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'status' => LoyaltyMember::STATUS_ACTIVE,
            ]);
        });
    }

    public function uniqueToken(): string
    {
        do {
            $token = Str::lower(Str::random(40));
        } while (LoyaltyMember::where('token', $token)->exists());

        return $token;
    }

    /**
     * Registra una operación que suma o ajusta unidades del cliente.
     */
    public function register(
        LoyaltyMember $member,
        LoyaltyActivityType $type,
        int $units,
        ?string $reason = null,
        ?User $user = null,
        ?LoyaltyReward $reward = null,
    ): LoyaltyActivity {
        return DB::transaction(function () use ($member, $type, $units, $reason, $user, $reward) {
            $counter = $type->counter();
            $before = $counter ? (int) $member->{$counter} : 0;

            if ($counter) {
                $member->{$counter} = max(0, $before + $units);
            }

            $member->last_activity_at = now();
            $member->save();

            $activity = LoyaltyActivity::create([
                'business_id' => $member->business_id,
                'loyalty_member_id' => $member->id,
                'reward_id' => $reward?->id,
                'user_id' => $user?->id,
                'type' => $type,
                'units' => $units,
                'reason' => $reason,
            ]);

            if ($counter && $member->{$counter} > $before) {
                $this->logRewardsEarned($member, $counter, $before, (int) $member->{$counter}, $user);
            }

            return $activity;
        });
    }

    /**
     * Canjea una recompensa disponible descontando sus unidades.
     */
    public function redeem(
        LoyaltyMember $member,
        LoyaltyReward $reward,
        ?User $user = null,
        ?string $reason = null,
    ): ?LoyaltyActivity {
        if (! $reward->isAvailableFor($member)) {
            return null;
        }

        return DB::transaction(function () use ($member, $reward, $user, $reason) {
            $counter = $reward->requirement_type->counter();
            $member->{$counter} = max(0, (int) $member->{$counter} - $reward->requirement_units);
            $member->last_activity_at = now();
            $member->save();

            return LoyaltyActivity::create([
                'business_id' => $member->business_id,
                'loyalty_member_id' => $member->id,
                'reward_id' => $reward->id,
                'user_id' => $user?->id,
                'type' => LoyaltyActivityType::RewardRedeemed,
                'units' => -1 * $reward->requirement_units,
                'reason' => $reason ?? 'Canje: '.$reward->name,
            ]);
        });
    }

    /**
     * Detecta recompensas desbloqueadas al cruzar un umbral y deja registro.
     */
    private function logRewardsEarned(LoyaltyMember $member, string $counter, int $before, int $after, ?User $user): void
    {
        $rewards = LoyaltyReward::ofBusiness($member->business)
            ->where('is_active', true)
            ->get()
            ->filter(function (LoyaltyReward $reward) use ($counter, $before, $after) {
                if ($reward->isExpired() || $reward->requirement_type->counter() !== $counter) {
                    return false;
                }

                return $reward->requirement_units > $before && $reward->requirement_units <= $after;
            });

        foreach ($rewards as $reward) {
            LoyaltyActivity::create([
                'business_id' => $member->business_id,
                'loyalty_member_id' => $member->id,
                'reward_id' => $reward->id,
                'user_id' => $user?->id,
                'type' => LoyaltyActivityType::RewardEarned,
                'units' => 0,
                'reason' => 'Recompensa desbloqueada: '.$reward->name,
            ]);
        }
    }
}
