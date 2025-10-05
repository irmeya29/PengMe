@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{
    --primary:#175278; --accent:#FECA0A; --hover:#4A90B6;
    --bg:#f7f9fb; --muted:#6c757d;
  }
  body{ background:var(--bg); }

  /* Boutons */
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-primary{ border-color:var(--primary); color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--hover); border-color:var(--hover); color:#fff; }

  /* En-tête */
  .page-title{ color:var(--primary); }
  .chip{ font-size:.85rem; padding:.25rem .6rem; border-radius:999px; border:1px solid #e9ecef; background:#fff; }
  .chip-accent{ background:rgba(254,202,10,.18); border-color:rgba(254,202,10,.35); color:#7a5a00; }
  .btn-chip{ padding:.1rem .4rem; font-size:.8rem; }

  /* Conteneurs */
  .toolbar{ background:#fff; border:1px solid #e9ecef; border-radius:12px; }
  .card{ border:none; border-radius:12px; background:#fff; }
  .card-header{ background:#fff; border-bottom:1px solid #eef2f4; }
  .shadow-soft{ box-shadow:0 8px 22px rgba(23,82,120,.08); }

  /* KPI */
  .kpi .icon{ width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:10px; background:#f1f4f7; color:var(--primary); }
  .kpi .title{ color:var(--muted); font-size:.85rem; }
  .mono{ font-family: ui-monospace, Menlo, Consolas, "Courier New", monospace; }

  /* Table */
  .table thead th{ border:0; color:var(--muted); font-weight:600; }
  .table tbody tr{ border-bottom:1px solid #f1f3f5; }
  .badge-soft-success{ background:rgba(25,135,84,.12); color:#198754; border:1px solid rgba(25,135,84,.25); }
  .badge-soft-danger{  background:rgba(220,53,69,.12); color:#dc3545; border:1px solid rgba(220,53,69,.22); }
  .badge-soft-warning{ background:rgba(254,202,10,.2); color:#7a5a00; border:1px solid rgba(254,202,10,.35); }
  .badge-soft-muted{   background:rgba(108,117,125,.12); color:#6c757d; border:1px solid rgba(108,117,125,.2); }

  /* Filtre horizontal */
  .filter-group .input-group-text{ background:#fff; border:0; color:var(--muted); }
  .filter-group .form-control{ border:0; }
  .filter-group .input-group{ border:1px solid #e9ecef; border-radius:8px; overflow:hidden; }
</style>
@endpush

@section('content')
<div class="container py-4">
  @php
    $companyName = $company->name ?? $company->label ?? 'Entreprise';
    $companyCode = $company->code ?? $company->company_code ?? null;
    $rangeLabel  = ($start && $end)
      ? \Carbon\Carbon::parse($start)->format('d/m/Y').' → '.\Carbon\Carbon::parse($end)->format('d/m/Y')
      : 'Mois en cours';
  @endphp

  {{-- En-tête --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h4 page-title mb-0">🏢 {{ $companyName }}</h1>
      @if($companyCode)
        <span class="chip chip-accent d-inline-flex align-items-center gap-1" title="Code entreprise">
          <i class="bi bi-upc-scan"></i>
          <strong>{{ $companyCode }}</strong>
          <button id="copyCode" type="button" class="btn btn-outline-primary btn-chip ms-1" title="Copier">
            <i class="bi bi-clipboard"></i>
          </button>
        </span>
      @endif
    </div>
    <span class="chip"><i class="bi bi-calendar3 me-1"></i> {{ $rangeLabel }}</span>
  </div>

  {{-- Ruban : Total à rembourser à l’administrateur --}}
  <div class="card shadow-soft mb-3">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded"
              style="width:40px;height:40px;background:#f1f4f7; color:var(--primary)">
          <i class="bi bi-bank"></i>
        </span>
        <div>
          <div class="fw-semibold" style="color:var(--primary)">Total à rembourser à l’administrateur (période)</div>
          <div class="text-muted small">
            Somme des virements <strong>réussis</strong> (montants finaux, frais inclus).
            <span class="ms-2">Virements succès : <strong>{{ $adminDueCount }}</strong></span>
          </div>
        </div>
      </div>
      <div class="h3 mb-0 mono">{{ number_format($adminDue,0,' ',' ') }} FCFA</div>
    </div>
  </div>

  {{-- Filtre horizontal minimal --}}
  <form method="get" action="{{ route('company.dashboard') }}" class="toolbar shadow-soft p-2 mb-4">
    <div class="d-flex flex-wrap align-items-center gap-2 filter-group">
      <div class="input-group input-group-sm" style="max-width:220px;">
        <span class="input-group-text">Du</span>
        <input type="date" name="start" class="form-control" value="{{ $start }}">
      </div>
      <div class="input-group input-group-sm" style="max-width:220px;">
        <span class="input-group-text">Au</span>
        <input type="date" name="end" class="form-control" value="{{ $end }}">
      </div>
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Appliquer</button>
      <a href="{{ route('company.dashboard') }}" class="btn btn-outline-primary btn-sm">Ce mois</a>
    </div>
  </form>

  {{-- KPIs --}}
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card shadow-soft p-3 kpi">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-people"></i></div>
          <div>
            <div class="title">Employés</div>
            <div class="h4 mb-0">{{ $employeesCount }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-soft p-3 kpi">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-list-check"></i></div>
          <div>
            <div class="title">Demandes</div>
            <div class="h4 mb-0">{{ $advancesCount }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-soft p-3 kpi">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-cash-coin"></i></div>
          <div>
            <div class="title">Total des demandes</div>
            <div class="h4 mb-0 mono">{{ number_format($totalAmount,0,' ',' ') }} <small class="text-muted">FCFA</small></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-soft p-3 kpi">
        <div class="d-flex align-items-center gap-3">
          <div class="icon"><i class="bi bi-clipboard2-check"></i></div>
          <div>
            <div class="title">Taux succès</div>
            <div class="h4 mb-0">{{ $successRate }}%</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Top employés + dernières demandes --}}
  <div class="row g-3">
    <div class="col-lg-5">
      <div class="card shadow-soft h-100">
        <div class="card-header">Top 5 employés (montant final)</div>
        <div class="card-body">
          @if($topEmployees->isEmpty())
            <div class="text-muted small">Aucune donnée.</div>
          @else
            @php $maxTop = max(1, $topEmployees->max('total')); @endphp
            <ul class="list-group list-group-flush">
              @foreach($topEmployees as $t)
                @php $ratio = min(100, round(($t->total / $maxTop) * 100)); @endphp
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="fw-semibold">{{ $t->employee?->last_name }} {{ $t->employee?->first_name }}</div>
                      <div class="small text-muted">{{ $t->employee?->matricule }}</div>
                    </div>
                    <div class="mono">{{ number_format($t->total,0,' ',' ') }} FCFA</div>
                  </div>
                  <div class="progress mt-2" style="height:6px;">
                    <div class="progress-bar" role="progressbar"
                         style="width: {{ $ratio }}%; background:linear-gradient(90deg, var(--primary), var(--hover));"
                         aria-valuenow="{{ $ratio }}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card shadow-soft h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Dernières demandes</span>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('advances.index',['start'=>$start,'end'=>$end]) }}">Tout voir</a>
        </div>
        <div class="card-body">
          @if($recent->isEmpty())
            <div class="text-muted small">Aucune demande récente.</div>
          @else
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr><th>Date</th><th>Salarié</th><th class="text-end">Final</th><th>Demande</th><th>Virement</th></tr>
                </thead>
                <tbody>
                  @foreach($recent as $a)
                    <tr>
                      <td class="text-muted small">{{ $a->created_at->format('d/m H:i') }}</td>
                      <td>
                        <div class="fw-semibold">{{ $a->employee?->last_name }} {{ $a->employee?->first_name }}</div>
                        <div class="small text-muted">{{ $a->employee?->matricule }}</div>
                      </td>
                      <td class="text-end mono">{{ number_format($a->amount_final,0,' ',' ') }} FCFA</td>
                      <td>
                        @if($a->status==='approved')
                          <span class="badge badge-soft-success rounded-pill">Approuvée</span>
                        @elseif($a->status==='rejected')
                          <span class="badge badge-soft-danger rounded-pill">Rejetée</span>
                        @else
                          <span class="badge badge-soft-muted rounded-pill">{{ ucfirst($a->status ?? 'En attente') }}</span>
                        @endif
                      </td>
                      <td>
                        @if($a->payout?->status==='success')
                          <span class="badge badge-soft-success rounded-pill">Succès</span>
                        @elseif($a->payout?->status==='failed')
                          <span class="badge badge-soft-danger rounded-pill">Échec</span>
                        @else
                          <span class="badge badge-soft-warning rounded-pill">En cours</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // Copier code entreprise
  (function(){
    const btn = document.getElementById('copyCode');
    if(!btn) return;
    btn.addEventListener('click', ()=>{
      const code = @json($companyCode ?? '');
      if(!code) return;
      navigator.clipboard.writeText(code).then(()=>{
        btn.classList.remove('btn-outline-primary'); btn.classList.add('btn-primary');
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        setTimeout(()=>{ btn.classList.add('btn-outline-primary'); btn.classList.remove('btn-primary'); btn.innerHTML='<i class="bi bi-clipboard"></i>'; }, 1200);
      });
    });
  })();
</script>
@endpush
