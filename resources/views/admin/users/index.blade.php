@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  :root { --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; --soft:#f7f9fb; --muted:#6c757d; }

  body{ background: var(--soft); }
  .mono{ font-family: ui-monospace, Menlo, Consolas, "Courier New", monospace; }

  /* Carte principale */
  .card-shell{ border:0; border-radius:16px; box-shadow:0 10px 24px rgba(23,82,120,.10); overflow:hidden; }
  .card-header-bar{
    background:#fff; border-bottom:1px solid #e9eef3;
    display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem;
    padding: .9rem 1rem;
  }
  .page-title{ color:var(--primary); margin:0; font-size:1.15rem; font-weight:700; }

  /* Barre d’actions */
  .toolbar{ display:flex; align-items:center; gap:.5rem; }
  .toolbar .form-control{ max-width: 260px; }
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-primary{ color:var(--primary); border-color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--hover); border-color:var(--hover); color:#fff; }
  .btn-accent{ background:var(--accent); border-color:var(--accent); color:#111; }
  .btn-accent:hover{ filter: brightness(.95); color:#111; }

  /* Tableau moderne */
  .table-wrap{ background:#fff; }
  .table-modern thead th{
    background: var(--primary); color:#fff; border:0; vertical-align: middle;
  }
  .table-modern tbody tr{ border-bottom:1px solid #eef2f5; }
  .table-modern tbody tr:last-child{ border-bottom:0; }

  /* Statuts */
  .badge-soft{
    border:1px solid rgba(0,0,0,.08);
    background:#f4f6f8; color:#222;
  }
  .badge-active{ background:rgba(25,135,84,.12); color:#198754; border:1px solid rgba(25,135,84,.25); }
  .badge-inactive{ background:#e9ecef; color:#495057; border:1px solid #dee2e6; }

  /* Groupe d’actions */
  .action-group{ display:inline-flex; gap:.35rem; flex-wrap:wrap; }
  .action-group .btn{ min-width: 96px; }

  /* Messages */
  .alert{ border-radius:12px; }

  @media (max-width: 576px){
    .toolbar .form-control{ max-width: 180px; }
    .action-group .btn{ min-width: unset; }
  }
</style>
@endpush

@section('content')
<div class="container py-4">

  <div class="card card-shell">
    {{-- En-tête --}}
    <div class="card-header-bar">
      <h1 class="page-title">👑 Administrateurs</h1>

      <form method="get" action="{{ route('admin.users.index') }}" class="toolbar">
       
        <a href="{{ route('admin.users.create') }}" class="btn btn-accent btn-sm">+ Nouvel admin</a>
      </form>
    </div>

    {{-- Messages flash --}}
    @if(session('ok'))
      <div class="alert alert-success alert-dismissible fade show m-3">
        {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show m-3">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Tableau --}}
    <div class="table-responsive table-wrap">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th style="width:70px;">#</th>
            <th>Nom</th>
            <th>Email</th>
            <th style="width:140px;">Statut</th>
            <th style="width:150px;">Créé le</th>
            <th class="text-end" style="width:340px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($admins as $a)
            <tr>
              <td class="mono">{{ $a->id }}</td>
              <td class="fw-semibold">{{ $a->name }}</td>
              <td class="text-muted">{{ $a->email }}</td>
              <td>
                @if($a->active)
                  <span class="badge badge-active">Actif</span>
                @else
                  <span class="badge badge-inactive">Inactif</span>
                @endif
              </td>
              <td class="text-muted">{{ optional($a->created_at)->format('d/m/Y') }}</td>
              <td class="text-end">
                <div class="action-group">
                  {{-- Éditer --}}
                  <a href="{{ route('admin.users.edit', $a) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Éditer
                  </a>

                  {{-- Activer / Désactiver --}}
                  <form method="post" action="{{ route('admin.users.toggle', $a) }}"
                        onsubmit="return confirm('Confirmer l\\'action ?')">
                    @csrf @method('PATCH')
                    @if($a->active)
                      <button class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-slash-circle me-1"></i> Désactiver
                      </button>
                    @else
                      <button class="btn btn-outline-success btn-sm">
                        <i class="bi bi-check2-circle me-1"></i> Activer
                      </button>
                    @endif
                  </form>

                  {{-- Supprimer --}}
                  <form method="post" action="{{ route('admin.users.destroy', $a) }}"
                        onsubmit="return confirm('Supprimer cet administrateur ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                      <i class="bi bi-trash me-1"></i> Supprimer
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                Aucun administrateur trouvé
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="p-3">
      {{ $admins->links() }}
    </div>
  </div>

</div>
@endsection
