@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root { --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; --soft:#f7f9fb; --muted:#6c757d; }

  body{ background:var(--soft); }
  .mono{ font-family: ui-monospace, Menlo, Consolas, "Courier New", monospace; }

  /* Carte */
  .card-shell{ border:0; border-radius:16px; box-shadow:0 10px 24px rgba(23,82,120,.10); overflow:hidden; }
  .card-shell .card-header{
    background:#fff; border-bottom:1px solid #e9eef3;
    padding:1rem 1.25rem;
  }
  .page-title{ color:var(--primary); font-weight:700; font-size:1.1rem; margin:0; }

  /* Boutons brand */
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-secondary{ border-color:#d0d7de; color:#34495e; }
  .btn-outline-secondary:hover{ background:#f1f4f7; color:#1c2b34; }

  /* Inputs & focus */
  .form-control:focus{
    border-color: var(--accent);
    box-shadow: 0 0 0 .2rem rgba(254,202,10,.25);
  }
  .form-floating>.form-control, .form-floating>.form-select { padding-top: 1.25rem; padding-bottom: .5rem; }
  .form-floating>label{ color:#6b7b87; }

  /* Input group (icône œil) */
  .input-group-text{ background:#fff; border-left:0; }
  .input-group .form-control{ border-right:0; }

  /* Switch actif */
  .form-check-input:checked{ background-color: var(--primary); border-color: var(--primary); }

  .alert{ border-radius:12px; }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 720px;">

  <div class="card card-shell">
    <div class="card-header">
      <h1 class="page-title">Éditer administrateur</h1>
    </div>

    @if(session('ok'))
      <div class="alert alert-success alert-dismissible fade show m-3 mb-0">
        {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show m-3 mb-0">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form method="post" action="{{ route('admin.users.update', $admin) }}" class="p-3 p-md-4">
      @csrf @method('PUT')

      {{-- Nom --}}
      <div class="form-floating mb-3">
        <input type="text" name="name" id="name" class="form-control" placeholder="Nom" required
               value="{{ old('name', $admin->name) }}" autofocus>
        <label for="name"><i class="bi bi-person me-1 text-muted"></i> Nom</label>
      </div>

      {{-- Email --}}
      <div class="form-floating mb-3">
        <input type="email" name="email" id="email" class="form-control" placeholder="Email" required
               value="{{ old('email', $admin->email) }}">
        <label for="email"><i class="bi bi-envelope me-1 text-muted"></i> Email</label>
      </div>

      <div class="alert alert-light border small mb-3">
        Laissez les champs mot de passe vides pour ne pas le changer.
      </div>

      <div class="row g-3">
        {{-- Nouveau mot de passe --}}
        <div class="col-md-6">
          <label for="password" class="form-label">Nouveau mot de passe</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control">
            <span class="input-group-text" role="button" onclick="togglePwd('password', this)" title="Afficher/Masquer">
              <i class="bi bi-eye"></i>
            </span>
          </div>
        </div>

        {{-- Confirmation --}}
        <div class="col-md-6">
          <label for="password_confirmation" class="form-label">Confirmation</label>
          <div class="input-group">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            <span class="input-group-text" role="button" onclick="togglePwd('password_confirmation', this)" title="Afficher/Masquer">
              <i class="bi bi-eye"></i>
            </span>
          </div>
        </div>
      </div>

      {{-- Actif --}}
      <div class="form-check form-switch my-4">
        <input type="hidden" name="active" value="0"> {{-- envoie 0 si décoché --}}
        <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
               {{ old('active', $admin->active) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Actif</label>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-save2 me-1"></i> Enregistrer</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
      </div>
    </form>
  </div>

</div>
@endsection

@push('scripts')
<script>
  function togglePwd(id, el){
    const input = document.getElementById(id);
    const icon = el.querySelector('i');
    if(!input) return;
    const isPwd = input.type === 'password';
    input.type = isPwd ? 'text' : 'password';
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
  }
</script>
@endpush
