@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{
    --primary:#175278;
    --accent:#FECA0A;
    --text-dark:#0f2533;
  }
  body{ background:#f7f8fa; }

  /* Harmonisation primaire / accent */
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ filter:brightness(0.95); }
  .btn-accent{
    background:var(--accent); border-color:var(--accent);
    color:var(--text-dark); font-weight:600;
  }
  .btn-accent:hover{ filter:brightness(0.97); color:var(--text-dark); }
  .btn-outline-primary{ border-color:var(--primary); color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--primary); color:#fff; }

  .page-header{ border-bottom:1px solid #e9ecef; margin-bottom:1rem; padding-bottom:.5rem; }
  .card{ border:none; border-radius:16px; }
  .shadow-soft{ box-shadow:0 6px 20px rgba(23,82,120,.08); }

  /* Table */
  .table thead th{ background:var(--primary); color:#fff; border:0; }
  .table-hover tbody tr:hover{ background:#f2f6f9; }
  .badge-soft-primary{
    background:rgba(23,82,120,.12); color:var(--primary); border:1px solid rgba(23,82,120,.25);
  }
  .badge-soft-danger{
    background:rgba(220,53,69,.12); color:#dc3545; border:1px solid rgba(220,53,69,.2);
  }

  /* Barre de recherche compacte */
  .search-input{ width:260px; }
  @media (max-width: 576px){ .search-input{ width:100%; } }

  /* Espacement boutons d’actions en table */
  .table-actions{ gap:.5rem; }            /* espace horizontal entre boutons */
  .table-actions .btn{ padding:.35rem .6rem; }
</style>
@endpush

@section('content')
<div class="container py-4">

  {{-- Header --}}
  <div class="page-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
            style="width:40px;height:40px;background:rgba(23,82,120,.1);color:var(--primary)">
        <i class="bi bi-people-fill"></i>
      </span>
      <h1 class="h4 mb-0" style="color:var(--primary)">Gestion des employés</h1>
      {{-- <small class="text-muted">…</small>  — supprimé --}}
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center">
      {{-- Recherche compacte --}}
      <form class="d-flex align-items-center gap-2" method="get" action="{{ route('employees.index') }}">
        <input type="text" name="search" class="form-control form-control-sm search-input"
               placeholder="Nom, matricule, email…" value="{{ $s }}">
        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
        @if($s)
          <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">Réinit.</a>
        @endif
      </form>

      {{-- Actions --}}
      <a href="{{ route('employees.create') }}" class="btn btn-accent shadow-soft">
        <i class="bi bi-person-plus"></i> Nouvel employé
      </a>
      <a href="{{ route('employees.import.form') }}" class="btn btn-outline-primary">
        <i class="bi bi-upload"></i> Import CSV
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
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

  {{-- Tableau --}}
  <div class="card shadow-soft">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Matricule</th>
              <th>Nom complet</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th class="text-end">Salaire</th>
              <th>Éligible</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($employees as $e)
              <tr>
                <td class="fw-semibold">{{ $e->matricule }}</td>
                <td>{{ $e->last_name }} {{ $e->first_name }}</td>
                <td>{{ $e->email }}</td>
                <td>{{ $e->phone }}</td>
                <td class="text-end text-nowrap">{{ number_format($e->monthly_salary, 0, ' ', ' ') }} FCFA</td>
                <td>
                  @if($e->eligible)
                    <span class="badge badge-soft-primary rounded-pill">Oui</span>
                  @else
                    <span class="badge badge-soft-danger rounded-pill">Non</span>
                  @endif
                </td>
                <td class="text-center">
                  {{-- Remplace le btn-group par un flex avec gap pour aérer --}}
                  <div class="d-inline-flex align-items-center table-actions">
                    <a href="{{ route('employees.edit', $e) }}" class="btn btn-sm btn-accent" title="Modifier">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form method="post" action="{{ route('employees.destroy', $e) }}"
                          onsubmit="return confirm('Supprimer cet employé ?')" class="m-0">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="bi bi-info-circle me-1"></i> Aucun employé trouvé
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Pagination alignée à droite --}}
  <div class="mt-3 d-flex justify-content-end">
    {{ $employees->links() }}
  </div>

</div>
@endsection
