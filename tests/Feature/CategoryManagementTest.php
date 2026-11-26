<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_categories_and_changes_reach_new_report_form(): void
    {
        $admin = $this->account('Admin');
        $pelapor = $this->account('User');

        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('Kelola Kategori');
        $this->get(route('admin.categories.create'))->assertOk()->assertSee('Tambah Kategori');
        $this->post(route('admin.categories.store'), ['name' => 'Toko Palsu'])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Toko Palsu')->firstOrFail();
        $this->get(route('admin.categories.index'))->assertOk()->assertSee('Toko Palsu');
        $this->get(route('admin.categories.edit', $category))->assertOk()->assertSee('Toko Palsu');
        $this->patch(route('admin.categories.update', $category), ['name' => 'Toko Online Palsu'])
            ->assertRedirect(route('admin.categories.index'));

        $this->actingAs($pelapor)->get(route('reports.create'))
            ->assertOk()
            ->assertSee('Toko Online Palsu');

        $this->actingAs($admin)->patch(route('admin.categories.status', $category), ['is_active' => '0'])
            ->assertRedirect(route('admin.categories.index'));
        $this->get(route('admin.categories.index'))->assertOk()->assertSee('Nonaktif');

        $this->actingAs($pelapor)->get(route('reports.create'))
            ->assertOk()
            ->assertDontSee('Toko Online Palsu')
            ->assertSee('Belum ada kategori aktif.');
        $this->from(route('reports.create'))->post(route('reports.store'), [
            'category_id' => $category->id,
            'reported_value' => '081234567890',
            'chronology' => 'Kronologi laporan penipuan.',
            'evidence_file' => UploadedFile::fake()->create('bukti.pdf', 50, 'application/pdf'),
        ])->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('reports', 0);

        $this->actingAs($admin)->patch(route('admin.categories.status', $category), ['is_active' => '1'])
            ->assertRedirect(route('admin.categories.index'));
        $this->actingAs($pelapor)->get(route('reports.create'))->assertOk()->assertSee('Toko Online Palsu');
    }

    public function test_category_management_requires_admin_and_names_must_be_unique(): void
    {
        $category = Category::create(['name' => 'Investasi Palsu']);

        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->actingAs($this->account('Verifier'))->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($this->account('User'))->post(route('admin.categories.store'), ['name' => 'Kategori Baru'])
            ->assertForbidden();
        $this->patch(route('admin.categories.status', $category), ['is_active' => '0'])->assertForbidden();

        $this->actingAs($this->account('Admin'))->post(route('admin.categories.store'), ['name' => $category->name])
            ->assertSessionHasErrors('name');
        $this->patch(route('admin.categories.update', $category), ['name' => $category->name])
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_inactive_category_is_preserved_in_revision_and_moderation(): void
    {
        $pelapor = $this->account('User');
        $original = Category::create(['name' => 'Kategori Lama', 'is_active' => false]);
        $unavailable = Category::create(['name' => 'Kategori Tersembunyi', 'is_active' => false]);
        $active = Category::create(['name' => 'Kategori Tersedia']);
        $report = Report::create([
            'user_id' => $pelapor->id,
            'category_id' => $original->id,
            'reported_type' => 'phone',
            'reported_value' => '+6281234567890',
            'chronology' => 'Kronologi laporan penipuan.',
            'evidence_file' => 'evidences/bukti.jpg',
            'status' => 'need_revision',
        ]);

        $this->actingAs($pelapor)->get(route('reports.edit', $report))
            ->assertOk()
            ->assertSee($original->name . ' (kategori lama, nonaktif)')
            ->assertSee($active->name)
            ->assertDontSee($unavailable->name);

        $revision = ['reported_value' => '081234567890', 'chronology' => 'Kronologi sudah diperbaiki.'];
        $this->put(route('reports.update', $report), $revision + ['category_id' => $unavailable->id])
            ->assertSessionHasErrors('category_id');
        $this->put(route('reports.update', $report), $revision + ['category_id' => $original->id])
            ->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'category_id' => $original->id,
            'status' => 'pending',
        ]);

        $report->refresh()->update(['status' => 'pending_admin_review']);
        $this->actingAs($this->account('Admin'))->get(route('admin.index'))
            ->assertOk()
            ->assertSeeInOrder([$report->reported_value, $original->name, 'Tinjau']);

        $this->patch(route('admin.reports.approve', $report), [
            'public_summary' => 'Laporan penipuan telah ditinjau dan bukti diperiksa.',
        ])->assertRedirect(route('admin.index'));
        $this->get(route('admin.index'))->assertOk()
            ->assertSeeInOrder(['Riwayat keputusan laporan', $report->reported_value, $original->name, 'Terverifikasi', 'Lihat Detail']);
    }

    private function account(string $role): User
    {
        return User::factory()->create(['role_id' => Role::create(['name' => $role])->id]);
    }
}
