@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{
    --primary:#175278;
    --accent:#FECA0A;
    --hover:#4A90B6;
    --soft:#f7f8fa;
    --text:#0f2533;
  }
  body{ background:var(--soft); }

  /* Boutons */
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-primary{ border-color:var(--primary); color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--hover); border-color:var(--hover); color:#fff; }
  .btn-accent{ background:var(--accent); border-color:var(--accent); color:var(--text); font-weight:600; }
  .btn-accent:hover{ filter:brightness(0.97); color:var(--text); }
  .btn-nowrap{ white-space: nowrap; }

  /* Cards & table */
  .shadow-soft{ box-shadow:0 10px 30px rgba(23,82,120,.08); }
  .card{ border:none; border-radius:16px; }
  .table thead th{ background:var(--primary); color:#fff; border:0; }
  .table-hover tbody tr:hover{ background:#f2f6f9; }

  /* Badges soft */
  .badge-soft-success{ background:rgba(25,135,84,.12); color:#198754; border:1px solid rgba(25,135,84,.25); }
  .badge-soft-danger{ background:rgba(220,53,69,.12); color:#dc3545; border:1px solid rgba(220,53,69,.22); }
  .badge-soft-warning{ background:rgba(254,202,10,.2); color:#7a5a00; border:1px solid rgba(254,202,10,.35); }
  .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; border:1px solid #e9ecef; background:#fff; }

  /* Toolbar filtres */
  .filters-toolbar .form-control,
  .filters-toolbar .form-select{ height: 36px; }
  .filters-toolbar .search-input{ max-width: 260px; }
  @media (max-width: 576px){ .filters-toolbar .search-input{ max-width:100%; } }

  /* Actions table */
  .table-actions{ gap:.5rem; }
  .table-actions .btn{ padding:.35rem .6rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0" style="color:var(--primary)">💸 Demandes d’avances</h1>
  </div>

  {{-- Messages flash --}}
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show shadow-soft">
      <i class="bi bi-check-circle me-1"></i> {{ session('ok') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-soft">
      <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Période + export --}}
  <div class="card shadow-soft mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
              style="width:36px;height:36px;background:rgba(23,82,120,.08);color:var(--primary)">
          <i class="bi bi-calendar3"></i>
        </span>
        <span class="fw-semibold" style="color:var(--primary)">Filtrer par période</span>
      </div>
      <form method="get" action="{{ route('advances.export.sage') }}" class="m-0">
        <input type="hidden" name="start" value="{{ request('start') }}">
        <input type="hidden" name="end" value="{{ request('end') }}">
        <button class="btn btn-accent btn-sm btn-nowrap">
          <i class="bi bi-filetype-csv me-1"></i> Export Sage
        </button>
      </form>
    </div>

    <div class="card-body">
      <form method="get" action="{{ route('advances.index') }}" class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Du</label>
          <input type="date" name="start" class="form-control form-control-sm" value="{{ request('start') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Au</label>
          <input type="date" name="end" class="form-control form-control-sm" value="{{ request('end') }}">
        </div>
        <div class="col-md-6 d-flex gap-2 mt-3 mt-md-0">
          <button class="btn btn-outline-primary btn-sm"><i class="bi bi-funnel me-1"></i> Appliquer</button>
          @if(request()->hasAny(['start','end']))
            <a href="{{ route('advances.index') }}" class="btn btn-link btn-sm text-decoration-none">Ce mois</a>
          @endif
        </div>
      </form>
    </div>

    <div class="card-footer bg-white text-muted small">
      @php
        $hasRange = request('start') || request('end');
        $rangeLabel = $hasRange
          ? (request('start') ? \Carbon\Carbon::parse(request('start'))->format('d/m/Y') : '…')
            .' → '.
            (request('end') ? \Carbon\Carbon::parse(request('end'))->format('d/m/Y') : '…')
          : 'Mois en cours';
      @endphp
      Période affichée : <strong>{{ $rangeLabel }}</strong>
    </div>
  </div>

  {{-- Recherche + filtres --}}
  <form class="filters-toolbar d-flex flex-wrap gap-2 align-items-center mb-3" method="get" action="{{ route('advances.index') }}">
    <input type="text" name="search" class="form-control form-control-sm search-input"
           placeholder="Nom, matricule, email…" value="{{ $s ?? '' }}">

    <select class="form-select form-select-sm" name="status" style="max-width: 190px;">
      <option value="">Demande (tous)</option>
      <option value="approved" {{ ($status ?? '')==='approved'?'selected':'' }}>Approuvée</option>
      <option value="rejected" {{ ($status ?? '')==='rejected'?'selected':'' }}>Rejetée</option>
    </select>

    <select class="form-select form-select-sm" name="payout_status" style="max-width: 190px;">
      <option value="">Virement (tous)</option>
      <option value="pending" {{ ($payout ?? '')==='pending'?'selected':'' }}>En cours</option>
      <option value="success" {{ ($payout ?? '')==='success'?'selected':'' }}>Succès</option>
      <option value="failed"  {{ ($payout ?? '')==='failed'?'selected':''  }}>Échec</option>
    </select>

    {{-- Propager la période active --}}
    <input type="hidden" name="start" value="{{ request('start') }}">
    <input type="hidden" name="end" value="{{ request('end') }}">

    <button class="btn btn-primary btn-sm">
      <i class="bi bi-search me-1"></i> Rechercher
    </button>

    @if($s || $status || $payout)
      <a href="{{ route('advances.index', ['start'=>request('start'),'end'=>request('end')]) }}"
         class="btn btn-outline-primary btn-sm">Reset filtres</a>
    @endif
  </form>

  {{-- Tableau --}}
  <div class="table-responsive">
    <table class="table table-hover align-middle shadow-soft">
      <thead>
        <tr>
          <th>Date</th>
          <th>Salarié</th>
          <th class="text-end">Demandé</th>
          <th class="text-end">Frais</th>
          <th class="text-end">Final</th>
          <th>Statut</th>
          <th>Virement</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($advances as $a)
          <tr>
            <td>{{ $a->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <div class="fw-semibold">{{ $a->employee?->last_name }} {{ $a->employee?->first_name }}</div>
              <div class="small text-muted">{{ $a->employee?->matricule }} · {{ $a->employee?->email }}</div>
            </td>
            <td class="text-end text-nowrap">{{ number_format($a->amount_requested, 0, ' ', ' ') }} FCFA</td>
            <td class="text-end text-nowrap">{{ number_format($a->fee_fixed, 0, ' ', ' ') }} FCFA</td>
            <td class="text-end text-nowrap">{{ number_format($a->amount_final, 0, ' ', ' ') }} FCFA</td>
            <td>
              @if($a->status === 'approved')
                <span class="badge badge-soft-success rounded-pill">Approuvée</span>
              @elseif($a->status === 'rejected')
                <span class="badge badge-soft-danger rounded-pill">Rejetée</span>
              @else
                <span class="badge badge-soft-warning rounded-pill">{{ ucfirst($a->status ?? 'En attente') }}</span>
              @endif
            </td>
            <td>
              @php $pm = strtoupper($a->payout?->method ?? '-'); @endphp
              @if($a->payout?->status === 'success')
                <span class="badge badge-soft-success rounded-pill">Succès</span>
              @elseif($a->payout?->status === 'failed')
                <span class="badge badge-soft-danger rounded-pill">Échec</span>
              @else
                <span class="badge badge-soft-warning rounded-pill">En cours</span>
              @endif
              <div class="small text-muted mt-1">
                <span class="chip"><i class="bi bi-bank me-1"></i> {{ $pm }}</span>
              </div>
            </td>
            <td class="text-center">
              <div class="d-inline-flex align-items-center table-actions">
                <a href="{{ route('advances.show',$a) }}" class="btn btn-sm btn-outline-primary btn-nowrap">
                  <i class="bi bi-eye me-1"></i> Détails
                </a>

                @if($a->payout && $a->payout->status!=='success')
                  <form method="post" action="{{ route('advances.payout.success',$a) }}" class="m-0"
                        onsubmit="return confirm('Marquer ce virement comme SUCCÈS ?')">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-primary btn-nowrap">
                      <i class="bi bi-check2-circle me-1"></i> Succès
                    </button>
                  </form>
                @endif

                @if($a->payout && $a->payout->status!=='failed')
                  <form method="post" action="{{ route('advances.payout.failed',$a) }}" class="m-0"
                        onsubmit="return confirm('Marquer ce virement comme ÉCHEC ?')">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-primary btn-nowrap">
                      <i class="bi bi-x-octagon me-1"></i> Échec
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              <i class="bi bi-info-circle me-1"></i> Aucune demande trouvée
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-3 d-flex justify-content-end">
    {{ $advances->links() }}
  </div>

</div>
@endsection
