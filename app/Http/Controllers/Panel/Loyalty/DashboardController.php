<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Enums\LoyaltyActivityType;
use App\Models\LoyaltyActivity;
use Illuminate\View\View;

class DashboardController extends LoyaltyController
{
    public function index(): View
    {
        $business = $this->business();
        $program = $this->program($business);

        $members = $business->loyaltyMembers();
        $activities = LoyaltyActivity::ofBusiness($business);

        $recent = LoyaltyActivity::ofBusiness($business)
            ->with(['member', 'user'])
            ->latest()
            ->limit(8)
            ->get();

        return view('panel.loyalty.dashboard', [
            'business' => $business,
            'program' => $program,
            'stats' => [
                'members' => (clone $members)->count(),
                'active' => (clone $members)->where('last_activity_at', '>=', now()->subDays(30))->count(),
                'points_issued' => (int) (clone $activities)->where('type', LoyaltyActivityType::EarnPoints->value)->where('units', '>', 0)->sum('units'),
                'points_redeemed' => (int) abs((clone $activities)->whereIn('type', [LoyaltyActivityType::RedeemPoints->value, LoyaltyActivityType::RewardRedeemed->value])->sum('units')),
                'rewards' => $business->loyaltyRewards()->where('is_active', true)->count(),
                'redemptions' => (clone $activities)->where('type', LoyaltyActivityType::RewardRedeemed->value)->count(),
            ],
            'recent' => $recent,
        ]);
    }
}
