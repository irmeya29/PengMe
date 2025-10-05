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

  /* Cards / titres / ombres */
  .card{ border:none; border-radius:16px; }
  .shadow-soft{ box-shadow:0 10px 30px rgba(23,82,120,.08); }
  .card-header{ background:#fff; }
  .page-title{ color:var(--primary); }

  /* Chips / badges soft */
  .chip{ font-size:.8rem; padding:.25rem .55rem; border-radius:999px; border:1px solid #e9ecef; background:#fff; }
  .badge-soft-success{ background:rgba(25,135,84,.12); color:#198754; border:1px solid rgba(25,135,84,.25); }
  .badge-soft-danger{ background:rgba(220,53,69,.12); color:#dc3545; border:1px solid rgba(220,53,69,.22); }
  .badge-soft-warning{ background:rgba(254,202,10,.2); color:#7a5a00; border:1px solid rgba(254,202,10,.35); }
  .badge-soft-secondary{ background:rgba(108,117,125,.12); color:#6c757d; border:1px solid rgba(108,117,125,.2); }

  /* KV / Mono */
  .kv{ display:flex; justify-content:space-between; gap:1rem; }
  .kv + .kv{ margin-top:.5rem; }
  .mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }

  /* JSON */
  pre.json{ max-height:320px; overflow:auto; background:#fff; border:1px solid #e9ecef; border-radius:12px; }

  /* Timeline */
  .timeline{ position:relative; padding-left:28px; }
  .timeline::before{
    content:""; position:absolute; left:12px; top:0; bottom:0; width:2px;
    background:linear-gradient(to bottom, rgba(23,82,120,.2), rgba(23,82,120,.05));
  }
  .tl-item{ position:relative; margin-bottom:16px; }
  .tl-marker{
    position:absolute; left:4px; top:6px; width:16px; height:16px; border-radius:50%;
    background:var(--primary); border:3px solid #fff; box-shadow:0 0 0 2px rgba(23,82,120,.15);
  }
  .tl-content{
    background:#fff; border:1px solid #e9ecef; border-radius:12px; padding:10px 12px;
  }
  .tl-title{ font-weight:600; color:var(--text); }
  .tl-time{ font-size:.78rem; color:#6c757d; }
  .tl-meta{ font-size:.85rem; color:#6c757d; }
  .tl-success .tl-marker{ background:#198754; }
  .tl-danger .tl-marker{ background:#dc3545; }
  .tl-warning .tl-marker{ background:#FECA0A; }
  .tl-secondary .tl-marker{ background:#6c757d; }
</style>
@endpush

@section('content')
<div class="container py-4">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('advances.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-arrow-left"></i>
      </a>
      <h1 class="h5 page-title mb-0">Détail de la demande</h1>
    </div>
    <span class="chip"><i class="bi bi-hash me-1"></i> ADV-{{ $advance->id }}</span>
  </div>

  {{-- Flashs --}}
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show shadow-soft">
      <i class="bi bi-check-circle me-1"></i> {{ session('ok') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Résumé --}}
  @php
    $ds = $advance->status;
    $ps = $advance->payout?->status;
    $pm = strtoupper($advance->payout?->method ?? '-');
  @endphp
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <span class="chip"><i class="bi bi-calendar3 me-1"></i> {{ $advance->created_at->format('d/m/Y H:i') }}</span>
    @if($ds==='approved')
      <span class="badge badge-soft-success rounded-pill">Demande approuvée</span>
    @elseif($ds==='rejected')
      <span class="badge badge-soft-danger rounded-pill">Demande rejetée</span>
    @else
      <span class="badge badge-soft-secondary rounded-pill">{{ ucfirst($ds ?? 'En attente') }}</span>
    @endif

    @if($ps==='success')
      <span class="badge badge-soft-success rounded-pill">Virement : Succès</span>
    @elseif($ps==='failed')
      <span class="badge badge-soft-danger rounded-pill">Virement : Échec</span>
    @else
      <span class="badge badge-soft-warning rounded-pill">Virement : En cours</span>
    @endif
    <span class="chip"><i class="bi bi-bank me-1"></i> {{ $pm }}</span>
  </div>

  <div class="row g-3">
    {{-- Salarié --}}
    <div class="col-md-6">
      <div class="card shadow-soft h-100">
        <div class="card-header fw-semibold d-flex align-items-center gap-2">
          <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                style="width:32px;height:32px;background:rgba(23,82,120,.08);color:var(--primary)">
            <i class="bi bi-person-circle"></i>
          </span>
          <span>Salarié</span>
        </div>
        <div class="card-body">
          <div class="kv"><span>Nom</span><strong>{{ $advance->employee?->last_name }} {{ $advance->employee?->first_name }}</strong></div>
          <div class="kv"><span>Matricule</span><strong class="mono">{{ $advance->employee?->matricule }}</strong></div>
          <div class="kv"><span>Email</span><span>{{ $advance->employee?->email ?: '—' }}</span></div>
          <div class="kv"><span>Téléphone</span><span>{{ $advance->employee?->phone ?: '—' }}</span></div>
          <div class="kv"><span>Salaire mensuel</span><strong>{{ number_format($advance->employee?->monthly_salary,0,' ',' ') }} FCFA</strong></div>
        </div>
      </div>
    </div>

    {{-- Montants --}}
    <div class="col-md-6">
      <div class="card shadow-soft h-100">
        <div class="card-header fw-semibold d-flex align-items-center gap-2">
          <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                style="width:32px;height:32px;background:rgba(254,202,10,.25); color:#7a5a00;">
            <i class="bi bi-cash-coin"></i>
          </span>
          <span>Montants</span>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
              <span>Demandé</span>
              <strong>{{ number_format($advance->amount_requested,0,' ',' ') }} FCFA</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Frais fixes</span>
              <strong>{{ number_format($advance->fee_fixed,0,' ',' ') }} FCFA</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Montant final</span>
              <strong>{{ number_format($advance->amount_final,0,' ',' ') }} FCFA</strong>
            </li>
            @if(!is_null($advance->total_repayable ?? null))
              <li class="list-group-item d-flex justify-content-between">
                <span>Total remboursable</span>
                <strong>{{ number_format($advance->total_repayable,0,' ',' ') }} FCFA</strong>
              </li>
            @endif
          </ul>
          <div class="mt-3 small text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Montant final = Montant demandé + frais fixes (2 000 FCFA).
          </div>
        </div>
      </div>
    </div>

    {{-- Statuts & Virement --}}
    <div class="col-md-6">
      <div class="card shadow-soft h-100">
        <div class="card-header fw-semibold d-flex align-items-center gap-2">
          <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                style="width:32px;height:32px;background:rgba(23,82,120,.08);color:var(--primary)">
            <i class="bi bi-gear"></i>
          </span>
          <span>Statuts & virement</span>
        </div>
        <div class="card-body">
          <div class="kv">
            <span>Statut demande</span>
            <span>
              @if($ds==='approved')
                <span class="badge badge-soft-success rounded-pill">Approuvée</span>
              @elseif($ds==='rejected')
                <span class="badge badge-soft-danger rounded-pill">Rejetée</span>
              @else
                <span class="badge badge-soft-secondary rounded-pill">{{ ucfirst($ds ?? 'En attente') }}</span>
              @endif
            </span>
          </div>

          <hr>

          <div class="kv">
            <span>Statut virement</span>
            <span>
              @if($ps==='success')
                <span class="badge badge-soft-success rounded-pill">Succès</span>
              @elseif($ps==='failed')
                <span class="badge badge-soft-danger rounded-pill">Échec</span>
              @else
                <span class="badge badge-soft-warning rounded-pill">En cours</span>
              @endif
            </span>
          </div>
          <div class="kv">
            <span>Méthode</span>
            <strong>{{ $pm }}</strong>
          </div>
          <div class="kv">
            <span>Référence</span>
            <span class="d-flex align-items-center gap-2">
              <span class="mono">{{ $advance->payout?->reference ?? '—' }}</span>
              @if($advance->payout?->reference)
                <button id="copyRef" type="button" class="btn btn-sm btn-outline-primary" title="Copier">
                  <i class="bi bi-clipboard"></i>
                </button>
              @endif
            </span>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            @if($advance->payout && $advance->payout->status!=='success')
              <form method="post" action="{{ route('advances.payout.success',$advance) }}"
                    onsubmit="return confirm('Marquer ce virement comme SUCCÈS ?')">
                @csrf @method('PATCH')
                <button class="btn btn-primary btn-sm">
                  <i class="bi bi-check2-circle me-1"></i> Marquer succès
                </button>
              </form>
            @endif
            @if($advance->payout && $advance->payout->status!=='failed')
              <form method="post" action="{{ route('advances.payout.failed',$advance) }}"
                    onsubmit="return confirm('Marquer ce virement comme ÉCHEC ?')">
                @csrf @method('PATCH')
                <button class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-x-octagon me-1"></i> Marquer échec
                </button>
              </form>
            @endif
            <a href="{{ route('advances.index') }}" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-list-ul me-1"></i> Retour à la liste
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Meta virement (JSON) --}}
    @if(!empty($advance->payout?->meta))
      <div class="col-md-6">
        <div class="card shadow-soft h-100">
          <div class="card-header fw-semibold d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                  style="width:32px;height:32px;background:rgba(254,202,10,.25); color:#7a5a00;">
              <i class="bi bi-receipt"></i>
            </span>
            <span>Détails virement (meta)</span>
          </div>
          <div class="card-body">
            <pre class="json p-3 mono mb-0">{{ json_encode($advance->payout->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
          </div>
        </div>
      </div>
    @endif
  </div>

  {{-- Timeline / Historique --}}
  @php
    // 1) Base : évènements synthétiques
    $timeline = collect([
      [
        'key'   => 'created',
        'label' => 'Demande créée',
        'tag'   => 'Demande',
        'icon'  => 'bi-plus-circle',
        'badge' => 'badge-soft-secondary',
        'class' => 'tl-secondary',
        'date'  => $advance->created_at,
      ],
    ]);

    // 2) Status demande (si différent)
    if (!empty($advance->status) && $advance->status !== 'pending') {
      $timeline->push([
        'key'   => $advance->status,
        'label' => $advance->status === 'approved' ? 'Demande approuvée' : 'Demande rejetée',
        'tag'   => 'Demande',
        'icon'  => $advance->status === 'approved' ? 'bi-check2-circle' : 'bi-x-octagon',
        'badge' => $advance->status === 'approved' ? 'badge-soft-success' : 'badge-soft-danger',
        'class' => $advance->status === 'approved' ? 'tl-success' : 'tl-danger',
        // A défaut d’un champ dédié, on prend updated_at (à améliorer si tu as status_at)
        'date'  => $advance->updated_at ?? $advance->created_at,
      ]);
    }

    // 3) Payout (si existe)
    if ($advance->payout) {
      $p = $advance->payout;
      $timeline->push([
        'key'   => 'payout_created',
        'label' => 'Virement initié',
        'tag'   => 'Virement',
        'icon'  => 'bi-arrow-right-circle',
        'badge' => 'badge-soft-warning',
        'class' => 'tl-warning',
        'meta'  => 'Méthode : '.strtoupper($p->method ?? '-'),
        'date'  => $p->created_at ?? $advance->created_at,
      ]);

      if ($p->status === 'success') {
        $timeline->push([
          'key'   => 'payout_success',
          'label' => 'Virement confirmé',
          'tag'   => 'Virement',
          'icon'  => 'bi-check2-circle',
          'badge' => 'badge-soft-success',
          'class' => 'tl-success',
          'meta'  => 'Référence : '.($p->reference ?? '—'),
          'date'  => $p->updated_at ?? $p->created_at ?? now(),
        ]);
      } elseif ($p->status === 'failed') {
        $timeline->push([
          'key'   => 'payout_failed',
          'label' => 'Virement échoué',
          'tag'   => 'Virement',
          'icon'  => 'bi-x-octagon',
          'badge' => 'badge-soft-danger',
          'class' => 'tl-danger',
          'meta'  => 'Référence : '.($p->reference ?? '—'),
          'date'  => $p->updated_at ?? $p->created_at ?? now(),
        ]);
      } else {
        $timeline->push([
          'key'   => 'payout_pending',
          'label' => 'Virement en cours',
          'tag'   => 'Virement',
          'icon'  => 'bi-hourglass-split',
          'badge' => 'badge-soft-warning',
          'class' => 'tl-warning',
          'meta'  => 'Référence : '.($p->reference ?? '—'),
          'date'  => $p->updated_at ?? $p->created_at ?? now(),
        ]);
      }
    }

    // 4) Événements métiers optionnels (si tu exposes $advance->events ou $advance->logs)
    // Format attendu pour chacun : ['label'=>'...', 'tag'=>'...', 'icon'=>'bi-...', 'badge'=>'badge-soft-...', 'class'=>'tl-...', 'meta'=>'...', 'date'=>Carbon]
    $extra = [];
    if (property_exists($advance, 'events') && $advance->events) { $extra = $advance->events; }
    if (property_exists($advance, 'logs') && $advance->logs)     { $extra = $advance->logs;  }
    if (!empty($extra)) {
      foreach ($extra as $ev) {
        if (!isset($ev['date'])) continue;
        $timeline->push([
          'key'   => $ev['key']   ?? 'event',
          'label' => $ev['label'] ?? 'Évènement',
          'tag'   => $ev['tag']   ?? 'Système',
          'icon'  => $ev['icon']  ?? 'bi-dot',
          'badge' => $ev['badge'] ?? 'badge-soft-secondary',
          'class' => $ev['class'] ?? 'tl-secondary',
          'meta'  => $ev['meta']  ?? null,
          'date'  => $ev['date'],
        ]);
      }
    }

    // Tri chronologique
    $timeline = $timeline->sortBy('date')->values();
  @endphp

  <div class="card shadow-soft mt-3">
    <div class="card-header fw-semibold d-flex align-items-center gap-2">
      <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
            style="width:32px;height:32px;background:rgba(23,82,120,.08);color:var(--primary)">
        <i class="bi bi-clock-history"></i>
      </span>
      <span>Historique</span>
    </div>
    <div class="card-body">
      @if($timeline->isEmpty())
        <div class="text-muted">Aucun évènement disponible.</div>
      @else
        <div class="timeline">
          @foreach($timeline as $ev)
            <div class="tl-item {{ $ev['class'] ?? '' }}">
              <span class="tl-marker"></span>
              <div class="tl-content shadow-soft">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi {{ $ev['icon'] }}"></i>
                    <span class="tl-title">{{ $ev['label'] }}</span>
                  </div>
                  <span class="badge {{ $ev['badge'] }} rounded-pill">{{ $ev['tag'] }}</span>
                </div>
                @if(!empty($ev['meta']))
                  <div class="tl-meta mt-1">{!! nl2br(e($ev['meta'])) !!}</div>
                @endif
                <div class="tl-time mt-2">
                  <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($ev['date'])->format('d/m/Y H:i') }}
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>

{{-- JS : copier la référence de virement --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('copyRef');
    if (!btn) return;
    btn.addEventListener('click', () => {
      const ref = @json($advance->payout?->reference ?? '');
      if (!ref) return;
      navigator.clipboard.writeText(ref).then(() => {
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-accent');
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        setTimeout(() => {
          btn.classList.remove('btn-accent');
          btn.classList.add('btn-outline-primary');
          btn.innerHTML = '<i class="bi bi-clipboard"></i>';
        }, 1400);
      });
    });
  });
</script>
@endsection
