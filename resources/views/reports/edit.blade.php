@extends('layouts.app')

@section('title', 'Perbaiki Laporan - CekDulu')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-lg-8">

        <div class="mb-4">
            <h3 class="fw-bold mb-0">Perbaiki Laporan</h3>
            <p class="text-muted small mb-0">
                Target: {{ $report->target_display }}, dikirim {{ $report->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <div class="alert alert-danger shadow-sm">
            <strong>Catatan dari Verifier</strong>
            <p class="mb-0">{{ $report->revision_note ?? 'Tidak ada catatan tambahan.' }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3 px-4">
                <h5 class="mb-0 fw-bold">Formulir Perbaikan</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data" id="editReportForm">
                    @csrf
                    @method('PUT')

                    <!-- Pilihan Jenis Laporan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Jenis yang Dilaporkan <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline p-2 border rounded-3 px-3 bg-light flex-fill">
                                <input class="form-check-input" type="radio" name="reported_type" id="type_phone" 
                                       value="phone" {{ old('reported_type', $report->reported_type) === 'phone' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="type_phone">
                                    <i class="bi bi-telephone text-primary me-1"></i> Nomor Telepon
                                </label>
                            </div>
                            <div class="form-check form-check-inline p-2 border rounded-3 px-3 bg-light flex-fill">
                                <input class="form-check-input" type="radio" name="reported_type" id="type_bank" 
                                       value="bank_account" {{ old('reported_type', $report->reported_type) === 'bank_account' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="type_bank">
                                    <i class="bi bi-credit-card-2-front text-primary me-1"></i> Rekening Bank / E-Wallet
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Field: Telepon -->
                    <div id="section-phone" class="{{ old('reported_type', $report->reported_type) === 'phone' ? '' : 'd-none' }}">
                        <div class="mb-3">
                            <label for="phone_input" class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('reported_value') is-invalid @enderror"
                                id="phone_input" name="phone_reported_value" 
                                value="{{ old('reported_type', $report->reported_type) === 'phone' ? old('reported_value', $report->reported_value) : '' }}"
                                placeholder="Contoh: 081234567890" inputmode="tel" maxlength="30"
                                data-phone-input="#phone-hint">
                            @if(old('reported_type', $report->reported_type) === 'phone')
                                @error('reported_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                            <div id="phone-hint" class="form-text">Bisa ditulis 0812..., +62812..., atau 62812...</div>
                        </div>
                    </div>

                    <!-- Form Field: Rekening Bank / E-Wallet -->
                    <div id="section-bank" class="{{ old('reported_type', $report->reported_type) === 'bank_account' ? '' : 'd-none' }}">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label fw-bold">Nama Bank / E-Wallet <span class="text-danger">*</span></label>
                            <select class="form-select @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name">
                                <option value="" selected disabled>-- Pilih Bank atau Dompet Digital --</option>
                                @foreach($banks as $bKey => $bLabel)
                                    <option value="{{ $bKey }}" {{ old('bank_name', $report->bank_name) === $bKey ? 'selected' : '' }}>
                                        {{ $bLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bank_account_input" class="form-label fw-bold">Nomor Rekening / Nomor Akun <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('reported_value') is-invalid @enderror"
                                id="bank_account_input" name="bank_reported_value" 
                                value="{{ old('reported_type', $report->reported_type) === 'bank_account' ? old('reported_value', $report->reported_value) : '' }}"
                                placeholder="Contoh: 1234567890" inputmode="numeric" maxlength="30">
                            @if(old('reported_type', $report->reported_type) === 'bank_account')
                                @error('reported_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="account_holder" class="form-label fw-bold">Nama Pemilik Rekening (Atas Nama) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_holder') is-invalid @enderror"
                                id="account_holder" name="account_holder" 
                                value="{{ old('account_holder', $report->account_holder) }}"
                                placeholder="Contoh: BUDI SANTOSO" maxlength="100">
                            @error('account_holder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Hidden input submitted as reported_value -->
                    <input type="hidden" name="reported_value" id="actual_reported_value" value="{{ old('reported_value', $report->reported_value) }}">

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Kategori Penipuan <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $report->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="chronology" class="form-label fw-bold">Kronologi Kejadian <span class="text-danger">*</span></label>
                        <textarea id="chronology" name="chronology" rows="6"
                                  class="form-control @error('chronology') is-invalid @enderror"
                                  required>{{ old('chronology', $report->chronology) }}</textarea>
                        @error('chronology')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="evidence_file" class="form-label fw-bold">Ganti Bukti Pendukung (opsional)</label>
                        <input type="file" id="evidence_file" name="evidence_file"
                               class="form-control @error('evidence_file') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">
                            Kosongkan jika bukti saat ini sudah sesuai.
                            <a href="{{ route('reports.evidence', $report->id) }}" target="_blank">Lihat bukti saat ini</a>.
                            Format JPG, PNG, atau PDF, maksimal 2 MB.
                        </div>
                        @error('evidence_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Kirim Perbaikan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    @include('partials.phone-input-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioPhone = document.getElementById('type_phone');
            const radioBank = document.getElementById('type_bank');
            const secPhone = document.getElementById('section-phone');
            const secBank = document.getElementById('section-bank');
            const phoneInput = document.getElementById('phone_input');
            const bankInput = document.getElementById('bank_account_input');
            const actualValue = document.getElementById('actual_reported_value');
            const form = document.getElementById('editReportForm');

            function updateView() {
                if (radioPhone.checked) {
                    secPhone.classList.remove('d-none');
                    secBank.classList.add('d-none');
                    actualValue.value = phoneInput.value;
                } else {
                    secPhone.classList.add('d-none');
                    secBank.classList.remove('d-none');
                    actualValue.value = bankInput.value;
                }
            }

            radioPhone.addEventListener('change', updateView);
            radioBank.addEventListener('change', updateView);

            phoneInput.addEventListener('input', function() {
                if (radioPhone.checked) actualValue.value = phoneInput.value;
            });

            bankInput.addEventListener('input', function() {
                if (radioBank.checked) actualValue.value = bankInput.value;
            });

            form.addEventListener('submit', function() {
                if (radioPhone.checked) {
                    actualValue.value = phoneInput.value;
                } else {
                    actualValue.value = bankInput.value;
                }
            });

            updateView();
        });
    </script>
@endpush
@endsection
