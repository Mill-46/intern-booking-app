<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalActionRequest;
use App\Models\Approval;
use App\Services\ApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function __construct(
        private readonly ApprovalService $approvalService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Approval::class);

        $approvals = Approval::with(['booking.vehicle', 'booking.user', 'booking.originSite', 'booking.destinationSite'])
            ->where('approver_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('approvals.index', compact('approvals'));
    }

    public function approve(ApprovalActionRequest $request, Approval $approval): RedirectResponse
    {
        $this->authorize('approve', $approval);

        $this->approvalService->approve($approval, $request);

        return back()->with('status', 'Persetujuan berhasil direkam.');
    }

    public function reject(ApprovalActionRequest $request, Approval $approval): RedirectResponse
    {
        $this->authorize('reject', $approval);

        $this->approvalService->reject($approval, $request);

        return back()->with('status', 'Penolakan berhasil direkam.');
    }
}
