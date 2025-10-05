@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{ --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; --soft:#f7f9fb; --muted:#6c757d; }
  body{ background:var(--soft); }
  .mono{ font-family: ui-monospace, Menlo, Consolas, "Courier New", monospace; }

  .card{ border:none; border-radius:14px; background:#fff; }
  .shadow-soft{ box-shadow:0 10px 24px rgba(23,82,120,.10); }

  .toolbar{ background:#fff; border:1px solid #e9eef3; border-radius:12px; }
  .filter .input-group-text{ background:#fff; border-right:0; color:var(--muted); }
  .filter .form-control{ border-left:0; }
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }

  .kpi{ padding:1rem; border:1px solid #e9eef3; border-radius:12px; background:#fff; }
  .kpi .icon{ width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#f1f4f7; color:var(--primary); }
  .kpi .title{ color:var(--muted); font-size:.85rem; }
  .kpi .value{ font-size:1.35rem; font-weight:700; }
  .sep{ height:1px; background:#eef2f5; margin:.5rem 0; }

  .ribbon{ display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1.1rem 1.25rem; border-radius:14px; background:#fff; border:1px solid #e9eef3; }
  .ribbon .icon{ width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:#f1f4f7; color:var(--primary); }
  .ribbon .title{ font-weight:600; color:#1c2b34; }
  .ribbon .subtitle{ color:var(--muted); font-size:.9rem; }
  .text-accent{ color:#7a5a00; }

  .list-top .list-group-item{ border:0; border-bottom:1px solid #eef2f5; }
  .bar{ height:6px; background:#eef2f5; border-radius:8px; overflow:hidden; }
  .bar > span{ display:block; height:100%; background:linear-gradient(90deg, var(--primary), var(--hover)); }
</style>
@endpush

@section('content')
<div class="container py-4">

  {{-- Titre + filtre période --}}
  <div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
    <div>
      <h1 class="h4 mb-0" style="color:var(--primary)">🛡️ Admin — Dashboard</h1>
      <div class="text-muted small">Période : <strong>{{ $periodLabel }}</strong></div>
    </div>
    <form method="get" action="{{ route('admin.dashboard') }}" class="toolbar p-2 p-md-3 d-flex flex-wrap align-items-end gap-2 filter">
      <div class="input-group input-group-sm" style="max-width:220px;">
        <span class="input-group-text">Du</span>
        <input type="date" name="start" class="form-control" value="{{ $start }}">
      </div>
      <div class="input-group input-group-sm" style="max-width:220px;">
        <span class="input-group-text">Au</span>
        <input type="date" name="end" class="form-control" value="{{ $end }}">
      </div>
      <button class="btn btn-primary btn-sm">Appliquer</button>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-link btn-sm">Ce mois</a>
    </form>
  </div>

  {{-- Ligne KPI Entreprises / Employés / Demandes --}}
  <div class="row g-3 mb-3">
    <div class="col-lg-4">
      <div class="kpi shadow-soft">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="icon"><i class="bi bi-buildings"></i></div>
            <div>
              <div class="title">Entreprises</div>
              <div class="value">{{ $companiesActive + $companiesInactive }}</div>
            </div>
          </div>
          <span class="badge bg-success-subtle text-success border">{{ $companiesActive }} actives</span>
        </div>
        <div class="sep"></div>
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted small">Inactives</span>
          <span class="fw-semibold">{{ $companiesInactive }}</span>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="kpi shadow-soft">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-people"></i></div>
          <div>
            <div class="title">Employés (global)</div>
            <div class="value">{{ $employeesTotal }}</div>
          </div>
        </div>
        <div class="sep"></div>
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted small">Ayant fait une demande (période)</span>
          <span class="fw-semibold">{{ $employeesWithRequests }}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-1">
          <span class="text-muted small">Avec virement succès</span>
          <span class="fw-semibold">{{ $employeesPaid }}</span>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="kpi shadow-soft">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-list-check"></i></div>
          <div>
            <div class="title">Demandes (période)</div>
            <div class="value">{{ $requestsCount }}</div>
          </div>
        </div>
        <div class="sep"></div>
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted small">Demandes + frais (tous statuts)</span>
          <span class="fw-semibold mono">{{ number_format($totalRequested,0,' ',' ') }} FCFA</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Montants clés --}}
  <div class="row g-3 mb-4">
    <div class="col-lg-6">
      <div class="ribbon shadow-soft">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-bank2"></i></div>
          <div>
            <div class="title">À recevoir des entreprises (succès)</div>
            <div class="subtitle">Montants finaux <strong>(demandes + frais)</strong> avec virement <strong>réussi</strong></div>
          </div>
        </div>
        <div class="h3 mb-0 mono">{{ number_format($totalDueSuccess,0,' ',' ') }} FCFA</div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="ribbon shadow-soft">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-graph-up-arrow"></i></div>
          <div>
            <div class="title">Marge réalisée (frais succès)</div>
            <div class="subtitle">Somme des frais fixes (2 000 FCFA) sur les virements <strong>réussis</strong></div>
          </div>
        </div>
        <div class="h3 mb-0 mono text-accent">{{ number_format($marginFees,0,' ',' ') }} FCFA</div>
      </div>
    </div>
  </div>

  {{-- Top 5 entreprises --}}
  <div class="card shadow-soft">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Top 5 entreprises (par nombre de demandes — période)</span>
      <span class="text-muted small">
        <i class="bi bi-info-circle me-1"></i> Affiche <strong>Demandé (tous statuts)</strong> et <strong>Reçu (succès)</strong>
      </span>
    </div>
    <div class="card-body list-top">
      @if($topCompanies->isEmpty())
        <div class="text-muted small">Aucune donnée sur la période.</div>
      @else
        @php $maxReq = max(1, $topCompanies->max('requests')); @endphp
        <ul class="list-group list-group-flush">
          @foreach($topCompanies as $row)
            @php $ratio = min(100, round(($row->requests / $maxReq) * 100)); @endphp
            <li class="list-group-item py-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ $row->name }}</div>
                  <div class="text-muted small">
                    {{ $row->requests }} demandes • {{ $row->success_cnt }} succès
                  </div>
                </div>
                <div class="text-end">
                  <div>Demandé (tous) : <span class="mono">{{ number_format($row->total_all,0,' ',' ') }} FCFA</span></div>
                  <div>Reçu (succès) : <span class="mono">{{ number_format($row->due_success,0,' ',' ') }} FCFA</span></div>
                  <div class="small text-muted">Frais succès : {{ number_format($row->fees_success,0,' ',' ') }} FCFA</div>
                </div>
              </div>
              <div class="bar mt-2"><span style="width: {{ $ratio }}%"></span></div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>

</div>
@endsection
