<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ReportReadyForModerationNotification;
use App\Notifications\ReportStatusChanged;
use App\Notifications\ReportSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $verifier;
    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $roleUser = Role::create(['name' => 'User']);
        $roleVerifier = Role::create(['name' => 'Verifier']);
        $roleAdmin = Role::create(['name' => 'Admin']);

        $this->user = User::factory()->create(['role_id' => $roleUser->id]);
        $this->verifier = User::factory()->create(['role_id' => $roleVerifier->id]);
        $this->admin = User::factory()->create(['role_id' => $roleAdmin->id]);

        $this->category = Category::create([
            'name' => 'Penipuan Online',
            'description' => 'Penipuan transaksi online',
        ]);
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_notification_list(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        $this->user->notify(new ReportStatusChanged($report, 'need_revision', 'Tolong perjelas kronologi'));

        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi');
        $response->assertSee('Laporan Perlu Revisi');
        $response->assertSee('Tolong perjelas kronologi');
    }

    public function test_user_can_filter_unread_notifications(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        $this->user->notify(new ReportStatusChanged($report, 'verified'));

        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->user)->get(route('notifications.index', ['filter' => 'unread']));
        $response->assertStatus(200);
        $response->assertSee('Laporan Disetujui & Dipublikasikan');

        // Mark as read and check again
        $notification->markAsRead();

        $responseAfterRead = $this->actingAs($this->user)->get(route('notifications.index', ['filter' => 'unread']));
        $responseAfterRead->assertStatus(200);
        $responseAfterRead->assertSee('Semua notifikasi sudah Anda baca.');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        $this->user->notify(new ReportStatusChanged($report, 'need_revision', 'Revisi diperlukan'));
        $notification = $this->user->unreadNotifications()->first();

        $response = $this->actingAs($this->user)->post(route('notifications.markAsRead', $notification->id));
        $response->assertSessionHas('success');

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        $this->user->notify(new ReportStatusChanged($report, 'need_revision', 'Revisi 1'));
        $this->user->notify(new ReportStatusChanged($report, 'rejected', 'Alasan penolakan'));

        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        $response = $this->actingAs($this->user)->post(route('notifications.markAllAsRead'));
        $response->assertSessionHas('success');

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_user_can_read_and_redirect(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        $this->user->notify(new ReportStatusChanged($report, 'need_revision'));
        $notification = $this->user->unreadNotifications()->first();

        $response = $this->actingAs($this->user)->get(route('notifications.readAndRedirect', $notification->id));

        $response->assertRedirect(route('reports.show', $report->id));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_delete_notification(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        $this->user->notify(new ReportStatusChanged($report, 'verified'));
        $notification = $this->user->notifications()->first();

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $notification->id));
        $response->assertSessionHas('success');

        $this->assertEquals(0, $this->user->notifications()->count());
    }

    public function test_verifiers_receive_notification_when_new_report_is_submitted(): void
    {
        Storage::fake('local');
        Notification::fake();

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($this->user)->post(route('reports.store'), [
            'category_id' => $this->category->id,
            'reported_value' => '081234567890',
            'chronology' => 'Penipuan pembelian gadget fiktif kronologi lengkap lebih dari 30 karakter.',
            'evidence_file' => $file,
        ]);

        $response->assertRedirect(route('dashboard'));

        Notification::assertSentTo(
            [$this->verifier],
            ReportSubmittedNotification::class,
            function ($notification) {
                return $notification->isRevision === false;
            }
        );
    }

    public function test_user_and_admin_receive_notifications_on_verifier_actions(): void
    {
        Notification::fake();

        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        // Verifier meminta revisi
        $this->actingAs($this->verifier)->patch(route('verifier.reports.update', $report->id), [
            'status' => 'need_revision',
            'revision_note' => 'Tolong upload bukti transfer',
        ]);

        Notification::assertSentTo(
            [$this->user],
            ReportStatusChanged::class,
            function ($notification) {
                return $notification->status === 'need_revision';
            }
        );

        // Verifier meneruskan ke admin
        $this->actingAs($this->verifier)->patch(route('verifier.reports.update', $report->id), [
            'status' => 'pending_admin_review',
        ]);

        Notification::assertSentTo(
            [$this->user],
            ReportStatusChanged::class,
            function ($notification) {
                return $notification->status === 'pending_admin_review';
            }
        );

        Notification::assertSentTo(
            [$this->admin],
            ReportReadyForModerationNotification::class
        );
    }

    public function test_user_receives_notification_when_admin_approves(): void
    {
        Notification::fake();

        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending_admin_review',
        ]);

        $responseApprove = $this->actingAs($this->admin)->patch(route('admin.reports.approve', $report->id), [
            'public_summary' => 'Nomor ini terbukti digunakan untuk modus penipuan online toko palsu.',
        ]);
        $responseApprove->assertRedirect(route('admin.index'));

        Notification::assertSentTo(
            [$this->user],
            ReportStatusChanged::class,
            function ($notification) {
                return $notification->status === 'verified';
            }
        );
    }

    public function test_user_receives_notification_when_admin_rejects(): void
    {
        Notification::fake();

        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi penipuan',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending_admin_review',
        ]);

        $responseReject = $this->actingAs($this->admin)->patch(route('admin.reports.reject', $report->id), [
            'rejection_reason' => 'Bukti percakapan tidak menyertakan bukti transfer valid.',
        ]);
        $responseReject->assertRedirect(route('admin.index'));

        Notification::assertSentTo(
            [$this->user],
            ReportStatusChanged::class,
            function ($notification) {
                return $notification->status === 'rejected';
            }
        );
    }
}
