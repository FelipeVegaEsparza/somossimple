<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KitRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitRequestsController extends Controller
{
    public function index(Request $request): View
    {
        $query = KitRequest::query()->orderByDesc('created_at');

        if ($request->filled('status') && in_array($request->input('status'), ['nueva', 'contactada', 'cerrada'], true)) {
            $query->where('status', $request->input('status'));
        }

        return view('admin.kitrequests.index', [
            'requests' => $query->simplePaginate(20)->withQueryString(),
            'nuevas' => KitRequest::where('status', 'nueva')->count(),
        ]);
    }

    public function markContacted(KitRequest $solicitud): RedirectResponse
    {
        $solicitud->update(['status' => 'contactada']);

        return back()->with('status', 'Solicitud marcada como contactada.');
    }

    public function destroy(KitRequest $solicitud): RedirectResponse
    {
        $solicitud->delete();

        return back()->with('status', 'Solicitud eliminada.');
    }
}
