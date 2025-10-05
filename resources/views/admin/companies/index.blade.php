@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{ --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; --muted:#6c757d; --soft:#f7f9fb; }
  body{ background:var(--soft); }

  .page-title{ color:var(--primary); }

  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-primary{ color:var(--primary); border-color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--hover); border-color:var(--hover); color:#fff; }
  .btn-warning{ background:var(--accent); border-color:var(--accent); color:#1b1b1b; }

  .search-wrap .input-group{ max-width: 360px; }
  .search-wrap .input-group-text{ background:#fff; border-right:0; color:var(--muted); }
  .search-wrap .form-control{ border-left:0; }

  .table-brand thead th{ background:var(--primary); color:#fff; border:0; }
  .table-brand tbody tr{ border-bottom:1px solid #edf2f6; }
  .mono{ font-family: ui-monospace, Menlo, Consolas, "Courier New", monospace; }

  .badge-code{ background:#fff; border:1px solid #e9ecef; color:#1f2a30; }
  .badge-soft-success{ background:rgba(25,135,84,.12); color:#198754; border:1px solid rgba(25,135,84,.25); }
  .badge-soft-danger{  background:rgba(220,53,69,.12); color:#dc3545; border:1px solid rgba(220,53,69,.22); }

  .actions{ display:flex; gap:.5rem; justify-content:flex-end; }
  .actions form{ display:inline; }
</style>
@endpush

@section('content')
<div class="container py-4">

  {{-- En-tête + actions --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h1 class="h4 page-title mb-2 mb-md-0">🏢 Entreprises</h1>

    <div class="d-flex align-items-center gap-2">
      {{-- Bouton Arrêter si impersonation en cours --}}
      @if(session()->has('impersonate_admin_id') && auth('web')->check())
        <form method="post" action="{{ route('admin.stopImpersonate') }}">
          @csrf
          <button class="btn btn-outline-dark btn-sm">
            <i class="bi bi-box-arrow-left me-1"></i> Arrêter l'impersonation
          </button>
        </form>
      @endif

      {{-- Recherche --}}
      <form class="search-wrap d-flex" method="get" action="{{ route('admin.companies.index') }}">
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Nom, email, code" value="{{ $s }}">
        </div>
        <button class="btn btn-primary btn-sm ms-2">Rechercher</button>
        @if($s)
          <a href="{{ route('admin.companies.index') }}" class="btn btn-link btn-sm ms-1">Réinitialiser</a>
        @endif
      </form>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('ok') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      {{ $errors->first() }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Tableau --}}
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 table-brand">
        <thead>
          <tr>
        
            <th>Nom</th>
            <th style="width:160px">Code</th>
            <th>Email</th>
            <th style="width:140px">Statut</th>
            <th class="text-end" style="width:280px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($companies as $c)
            <tr>
              
              <td class="fw-semibold">{{ $c->name }}</td>
              <td>
                <span class="badge badge-code mono">{{ $c->code }}</span>
                <button class="btn btn-sm btn-outline-primary ms-1" title="Copier"
                        onclick="navigator.clipboard.writeText('{{ $c->code }}')">
                  <i class="bi bi-clipboard"></i>
                </button>
              </td>
              <td>{{ $c->email }}</td>
              <td>
                @if($c->is_active)
                  <span class="badge badge-soft-success">Active</span>
                @else
                  <span class="badge badge-soft-danger">Suspendue</span>
                @endif
              </td>
              <td class="text-end">
                <div class="actions">
                  <form method="post" action="{{ route('admin.companies.toggle',$c) }}"
                        onsubmit="return confirm('{{ $c->is_active ? 'Suspendre cette entreprise ?' : 'Activer cette entreprise ?' }}');">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm {{ $c->is_active ? 'btn-warning' : 'btn-success' }}">
                      {{ $c->is_active ? 'Suspendre' : 'Activer' }}
                    </button>
                  </form>

                  <form method="post" action="{{ route('admin.companies.impersonate',$c) }}"
                        onsubmit="return confirm('Se connecter comme {{ $c->name }} ?');">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-person-bounding-box me-1"></i> Se connecter comme
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Aucune entreprise.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-3">
      {{ $companies->links() }}
    </div>
  </div>

  {{-- Bandeau au cas où (info persistante) --}}
  @if(session()->has('impersonate_admin_id') && auth('web')->check())
    <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center">
      <span>
        <i class="bi bi-person-check me-1"></i>
        Impersonation actif : vous êtes connecté comme une entreprise.
      </span>
      <form method="post" action="{{ route('admin.stopImpersonate') }}">
        @csrf
        <button class="btn btn-sm btn-outline-dark">Arrêter</button>
      </form>
    </div>
  @endif
</div>
@endsection
