<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BankAccountReportTest extends TestCase
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

    public function test_user_can_view_create_page_with_bank_options(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.create'));

        $response->assertStatus(200);
        $response->assertSee('Rekening Bank / E-Wallet');
        $response->assertSee('Bank Central Asia (BCA)');
    }

    public function test_user_can_submit_bank_account_report(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->actingAs($this->user)->post(route('reports.store'), [
            'reported_type' => 'bank_account',
            'bank_name' => 'BCA',
            'reported_value' => '1234567890',
            'account_holder' => 'BUDI SANTOSO',
            'category_id' => $this->category->id,
            'chronology' => 'Pelaku meminta transfer dana ke rekening BCA ini tapi barang tidak dikirim.',
            'evidence_file' => $file,
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'reported_type' => 'bank_account',
            'bank_name' => 'BCA',
            'reported_value' => '1234567890',
            'account_holder' => 'BUDI SANTOSO',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_bank_account_validation_rules(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('bukti.jpg');

        // Missing bank_name and account_holder
        $response = $this->actingAs($this->user)->post(route('reports.store'), [
            'reported_type' => 'bank_account',
            'reported_value' => '1234567890',
            'category_id' => $this->category->id,
            'chronology' => 'Kronologi penipuan belanja online',
            'evidence_file' => $file,
        ]);

        $response->assertSessionHasErrors(['bank_name', 'account_holder']);
    }

    public function test_public_search_finds_verified_bank_account(): void
    {
        Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'bank_account',
            'bank_name' => 'BRI',
            'reported_value' => '0987654321',
            'account_holder' => 'JOKO TINGKIR',
            'chronology' => 'Modus investasi bodong transfer ke rekening BRI.',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'verified',
            'public_summary' => 'Rekening ini terbukti digunakan untuk penipuan investasi palsu.',
        ]);

        // Search bank account
        $response = $this->get('/?tipe=bank_account&nomor=0987654321');

        $response->assertStatus(200);
        $response->assertSee('Rekening / Akun ini terindikasi penipuan!');
        $response->assertSee('0987654321');
        $response->assertSee('JOKO TINGKIR');
        $response->assertSee('Bank Rakyat Indonesia (BRI)');
    }

    public function test_verifier_and_admin_workflow_for_bank_account(): void
    {
        $report = Report::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'reported_type' => 'bank_account',
            'bank_name' => 'Mandiri',
            'reported_value' => '555666777',
            'account_holder' => 'SITI AMINAH',
            'chronology' => 'Penipuan tiket konser fiktif.',
            'evidence_file' => 'evidences/fake.jpg',
            'status' => 'pending',
        ]);

        // Verifier sees target display
        $responseVerifier = $this->actingAs($this->verifier)->get(route('verifier.reports.show', $report->id));
        $responseVerifier->assertStatus(200);
        $responseVerifier->assertSee('SITI AMINAH');
        $responseVerifier->assertSee('555666777');
        $responseVerifier->assertSee('Bank Mandiri');

        // Verifier forwards to admin
        $this->actingAs($this->verifier)->patch(route('verifier.reports.update', $report->id), [
            'status' => 'pending_admin_review',
        ]);

        $this->assertEquals('pending_admin_review', $report->fresh()->status);

        // Admin approves
        $responseAdmin = $this->actingAs($this->admin)->patch(route('admin.reports.approve', $report->id), [
            'public_summary' => 'Rekening terbukti terkait sindikat penipuan tiket konser.',
        ]);

        $responseAdmin->assertRedirect(route('admin.index'));
        $this->assertEquals('verified', $report->fresh()->status);
    }
}
