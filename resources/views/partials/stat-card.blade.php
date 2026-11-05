<div class="card shadow-sm h-100" style="border-left: 4px solid var(--bs-{{ $color ?? 'primary' }});">
    <div class="card-body d-flex justify-content-between align-items-start">
        <div>
            <p class="text-muted small fw-bold text-uppercase mb-1">{{ $label }}</p>
            <h2 class="fw-bold mb-0">{{ $value }}</h2>
        </div>
        @if(!empty($icon))
            <span class="text-{{ $color ?? 'primary' }} opacity-75">
                @include('partials.icon', ['name' => $icon])
            </span>
        @endif
    </div>
</div>