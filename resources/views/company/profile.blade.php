@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root { --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; --soft:#f7f9fb; --muted:#6c757d; }

  body{ background:var(--soft); }
  .mono{ font-family: ui-monospace, Menlo, Consolas, "Courier New", monospace; }

  .shell{ border:0; border-radius:16px; box-shadow:0 10px 24px rgba(23,82,120,.10); overflow:hidden; }
  .shell .head{ background:#fff; border-bottom:1px solid #e9eef3; padding:1rem 1.25rem; }
  .title{ color:var(--primary); font-weight:700; font-size:1.15rem; margin:0; }

  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-accent{ background:var(--accent); border-color:var(--accent); color:#111; }
  .btn-accent:hover{ filter:brightness(.95); color:#111; }

  .form-control:focus{ border-color: var(--hover); box-shadow: 0 0 0 .2rem rgba(74,144,182,.20); }
  .help{ color:var(--muted); font-size:.85rem; }

  .avatar{
    width:112px; height:112px; border-radius:14px; object-fit:cover;
    background:#f1f4f7; border:1px solid #e9eef3;
  }
  .badge-copy{ cursor:pointer; }
  .small-muted{ font-size:.9rem; color:#6b7b87; }

  .card-soft{ border:0; border-radius:14px; box-shadow:0 6px 16px rgba(23,82,120,.08); }
  .input-group-text{ background:#fff; border-left:0; }
  .input-group .form-control{ border-right:0; }

  .status-pill{ border:1px solid #e7edf2; background:#fff; color:#2f3b45; border-radius:999px; padding:.4rem .75rem; }
  .status-active{ border-color:rgba(25,135,84,.25); background:rgba(25,135,84,.08); color:#198754; }
  .status-inactive{ border-color:#e0e0e0; background:#f3f4f6; color:#495057; }
</style>
@endpush

@section('content')
@php($company = auth('web')->user())
<div class="container py-4" style="max-width: 1040px;">

  {{-- Messages flash --}}
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

  <div class="card shell">
    <div class="head d-flex flex-wrap align-items-center justify-content-between gap-2">
      <h1 class="title">🏢 Profil de l’entreprise</h1>
      <div class="d-flex align-items-center gap-2">
        <span class="status-pill {{ $company->is_active ? 'status-active' : 'status-inactive' }}">
          {{ $company->is_active ? 'Compte actif' : 'Compte suspendu' }}
        </span>
        <span class="small-muted">Code : <span id="companyCode" class="mono">{{ $company->code }}</span></span>
        <button type="button" class="btn btn-sm btn-accent badge-copy" onclick="copyCode()">Copier</button>
      </div>
    </div>

    <div class="p-3 p-md-4">
      <div class="row g-4">
        {{-- Colonne gauche : identité + logo --}}
        <div class="col-lg-4">
          <div class="card card-soft">
            <div class="card-body">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img id="logoPreview"
                     src="{{ $company->logo_path ? asset('storage/'.$company->logo_path).'?v='.time() : asset('assets/img/logo.png') }}"
                     alt="Logo" class="avatar">
                <div>
                  <div class="fw-semibold">{{ $company->name }}</div>
                  <div class="text-muted small">{{ $company->email }}</div>
                  @if($company->phone)
                    <div class="text-muted small">{{ $company->phone }}</div>
                  @endif
                </div>
              </div>

              <form method="post" action="{{ route('company.profile.logo') }}" enctype="multipart/form-data" class="d-grid gap-2">
                @csrf
                @method('PUT')
                <div>
                  <label class="form-label">Logo (PNG/JPG, carré conseillé)</label>
                  <input type="file" name="logo" accept="image/*" class="form-control" onchange="previewLogo(this)">
                  <div class="help mt-1">512×512 recommandé. Taille ≤ 2 Mo.</div>
                </div>
                <button class="btn btn-accent">Mettre à jour le logo</button>
              </form>
            </div>
          </div>
        </div>

        {{-- Colonne droite : infos + sécurité --}}
        <div class="col-lg-8">
          {{-- infos entreprise --}}
          <div class="card card-soft mb-3">
            <div class="card-body">
              <h6 class="mb-3" style="color:#175278;">Informations</h6>
              <form method="post" action="{{ route('company.profile.update') }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                  <label class="form-label">Nom de l’entreprise</label>
                  <input type="text" name="name" class="form-control" required value="{{ old('name',$company->name) }}">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" required value="{{ old('email',$company->email) }}">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Téléphone</label>
                  <input type="text" name="phone" class="form-control" value="{{ old('phone',$company->phone) }}">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Adresse</label>
                  <input type="text" name="address" class="form-control" value="{{ old('address',$company->address) }}">
                </div>

                <div class="d-flex gap-2 mt-2">
                  <button class="btn btn-primary">Enregistrer</button>
                  <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
              </form>
            </div>
          </div>

          {{-- sécurité --}}
          <div class="card card-soft">
            <div class="card-body">
              <h6 class="mb-3" style="color:#175278;">Sécurité</h6>
              <div class="help mb-2">Modifiez le mot de passe de connexion au portail entreprise.</div>

              <form method="post" action="{{ route('company.profile.password') }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                  <label class="form-label">Mot de passe actuel</label>
                  <div class="input-group">
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <span class="input-group-text" role="button" onclick="togglePwd('current_password', this)" title="Afficher/Masquer">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-6"></div>

                <div class="col-md-6">
                  <label class="form-label">Nouveau mot de passe</label>
                  <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <span class="input-group-text" role="button" onclick="togglePwd('password', this)" title="Afficher/Masquer">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Confirmation</label>
                  <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    <span class="input-group-text" role="button" onclick="togglePwd('password_confirmation', this)" title="Afficher/Masquer">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                  <button class="btn btn-primary">Mettre à jour le mot de passe</button>
                </div>
              </form>

            </div>
          </div>
        </div>

      </div> <!-- /row -->
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function copyCode(){
    const el = document.getElementById('companyCode');
    if (!el) return;
    navigator.clipboard.writeText(el.textContent.trim());
  }
  function previewLogo(input){
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('logoPreview').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
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
