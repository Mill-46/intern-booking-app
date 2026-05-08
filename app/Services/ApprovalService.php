<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Booking;
use App\Notifications\BookingStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function approve(Approval $approval, Request $request): Approval
    {
        $user = $request->user();

        if (! $user || $approval->approver_id !== $user->id) {
            abort(403);
        }

        DB::transaction(function () use ($approval, $request, $user): void {
            $lockedApproval = Approval::query()->lockForUpdate()->findOrFail($approval->id);

            if ($lockedApproval->status !== 'pending' || $lockedApproval->approver_id !== $user->id) {
                abort(422, 'Persetujuan ini sudah diproses sebelumnya.');
            }

            $booking = $lockedApproval->booking()->lockForUpdate()->firstOrFail();

            if ($lockedApproval->level === 2 && $booking->status !== Booking::STATUS_APPROVED_L1) {
                abort(422, 'Persetujuan level 2 hanya bisa dilakukan setelah level 1 disetujui.');
            }

            if ($lockedApproval->level === 1 && $booking->status !== Booking::STATUS_SUBMITTED) {
                abort(422);
            }

            $lockedApproval->update([
                'status' => 'approved',
                'comment' => $request->string('comment')->toString(),
                'acted_at' => now(),
            ]);

            $booking->update([
                'status' => $lockedApproval->level === 1
                    ? Booking::STATUS_APPROVED_L1
                    : Booking::STATUS_APPROVED_L2,
            ]);

            if ($lockedApproval->level === 1) {
                $nextApproval = $booking->approvals()->where('level', 2)->first();
                $nextApproval?->approver?->notify(new BookingStatusUpdatedNotification(
                    $booking,
                    'Pemesanan lolos persetujuan level 1 dan menunggu persetujuan Anda.'
                ));
            }

            if ($lockedApproval->level === 2) {
                $booking->user->notify(new BookingStatusUpdatedNotification(
                    $booking,
                    'Pemesanan telah lolos seluruh level persetujuan dan siap dikonfirmasi admin.'
                ));
            }
        });

        $booking = $approval->booking()->firstOrFail();

        $this->activityLogService->log(
            $request,
            'approve_booking',
            'Menyetujui pemesanan #' . $approval->booking_id . ' pada level ' . $approval->level,
            $user?->id,
            [
                'booking_id' => $approval->booking_id,
                'approval_id' => $approval->id,
                'level' => $approval->level,
                'to_status' => $booking->status,
            ]
        );

        return $approval;
    }

    public function reject(Approval $approval, Request $request): Approval
    {
        $user = $request->user();

        if (! $user || $approval->approver_id !== $user->id) {
            abort(403);
        }

        DB::transaction(function () use ($approval, $request, $user): void {
            $lockedApproval = Approval::query()->lockForUpdate()->findOrFail($approval->id);

            if ($lockedApproval->status !== 'pending' || $lockedApproval->approver_id !== $user->id) {
                abort(422, 'Persetujuan ini sudah diproses sebelumnya.');
            }

            $booking = $lockedApproval->booking()->lockForUpdate()->firstOrFail();

            if ($lockedApproval->level === 1 && $booking->status !== Booking::STATUS_SUBMITTED) {
                abort(422, 'Penolakan level 1 hanya bisa dilakukan saat status menunggu persetujuan.');
            }

            if ($lockedApproval->level === 2 && $booking->status !== Booking::STATUS_APPROVED_L1) {
                abort(422, 'Penolakan level 2 hanya bisa dilakukan setelah level 1 disetujui.');
            }

            $lockedApproval->update([
                'status' => 'rejected',
                'comment' => $request->string('comment')->toString(),
                'acted_at' => now(),
            ]);

            $booking->update([
                'status' => Booking::STATUS_REJECTED,
            ]);

            $booking->approvals()
                ->where('id', '!=', $lockedApproval->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'comment' => 'Pemesanan ditolak pada level persetujuan lain.',
                    'acted_at' => now(),
                ]);

            $booking->user->notify(new BookingStatusUpdatedNotification(
                $booking,
                'Pemesanan ditolak pada level ' . $lockedApproval->level . '.'
            ));
        });

        $this->activityLogService->log(
            $request,
            'reject_booking',
            'Menolak pemesanan #' . $approval->booking_id . ' pada level ' . $approval->level,
            $user?->id,
            [
                'booking_id' => $approval->booking_id,
                'approval_id' => $approval->id,
                'level' => $approval->level,
                'to_status' => Booking::STATUS_REJECTED,
            ]
        );

        return $approval;
    }
}
