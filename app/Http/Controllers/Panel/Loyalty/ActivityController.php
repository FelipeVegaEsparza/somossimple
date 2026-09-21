<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Models\LoyaltyActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends LoyaltyController
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $q = trim((string) $request->input('q'));

        $activities = LoyaltyActivity::ofBusiness($business)
            ->with(['member', 'reward', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('member', function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('panel.loyalty.activities.index', [
            'business' => $business,
            'program' => $this->program($business),
            'activities' => $activities,
            'q' => $q,
        ]);
    }
}
