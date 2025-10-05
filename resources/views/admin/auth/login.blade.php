@extends('layout.empty')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{
    --primary:#175278; --accent:#FECA0A; --hover:#4A90B6;
    --bg:#f5f8fb; --muted:#6c757d;
  }
  body{ background: linear-gradient(180deg,#f8fbff 0%,#f3f7fb 100%); }

  .auth-wrap{ min-height: 100vh; display:flex; align-items:center; justify-content:center; }
  .auth-card{ border:0; border-radius:16px; box-shadow:0 12px 30px rgba(23,82,120,.10); overflow:hidden; }
  .auth-card .topbar{ height:6px; background:var(--accent); }   /* bleu en haut */
  .auth-card .bottombar{ height:6px; background:var(--primary); } /* jaune en bas */
  .brand{ color:var(--primary); font-weight:700; letter-spacing:.2px; }
  .subtitle{ color:var(--muted); }

  .form-control{ border-radius:.6rem; }
  .input-group-text{ background:#fff; border-right:0; border-radius:.6rem 0 0 .6rem; color:var(--primary); }
  .form-control:focus{ border-color: var(--hover); box-shadow: 0 0 0 .2rem rgba(74,144,182,.15); }

  .btn-primary{ background:var(--primary); border-color:var(--primary); border-radius:.6rem; }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }

  .alert{ border-radius:.6rem; }
  .help-row{ display:flex; justify-content:space-between; align-items:center; }
  .form-check-input:checked{ background-color:var(--primary); border-color:var(--primary); }
</style>
@endpush

@section('content')
<div class="auth-wrap px-3">
  <div class="card auth-card" style="max-width: 460px; width:100%;">
    <div class="topbar"></div>

    <div class="card-body p-4 p-md-5">
      <div class="text-center mb-3">
         <div class="mb-3">
              <img src="{{ asset('assets/img/logo.png') }}" alt="Logo entreprise" height="110" class="mb-2">
            </div>
        <div class="brand h4 mb-0">Admin — Connexion</div>
        <div class="subtitle small">Accédez à votre espace d’administration</div>
      </div>

      @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger mb-3">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="post" action="{{ route('admin.doLogin') }}" id="adminLoginForm" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-3">
          <label class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="ex. admin@domaine.com"
                   autocomplete="username"
                   required>
            @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Mot de passe --}}
        <div class="mb-3">
          <label class="form-label">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Votre mot de passe"
                   autocomplete="current-password"
                   required>
            <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1" aria-label="Afficher/masquer le mot de passe">
              <i class="bi bi-eye"></i>
            </button>
            @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Options (facultatif) --}}
        <div class="help-row mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
            <label class="form-check-label" for="remember">Se souvenir de moi</label>
          </div>
          {{-- <a href="{{ route('admin.password.request') }}" class="small">Mot de passe oublié ?</a> --}}
        </div>

        <div class="d-grid">
          <button class="btn btn-primary" id="submitBtn">
            <span class="btn-text">Se connecter</span>
            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>

    <div class="bottombar"></div> {{-- barre jaune en bas --}}
  </div>
</div>

{{-- Scripts inline pour UX --}}
<script>
  (function(){
    // Toggle mot de passe
    const pwd = document.getElementById('password');
    const toggle = document.getElementById('togglePwd');
    if(toggle && pwd){
      toggle.addEventListener('click', function(){
        const isText = pwd.type === 'text';
        pwd.type = isText ? 'password' : 'text';
        this.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
      });
    }

    // Spinner + désactivation au submit
    const form = document.getElementById('adminLoginForm');
    const btn  = document.getElementById('submitBtn');
    if(form && btn){
      form.addEventListener('submit', function(){
        btn.disabled = true;
        btn.querySelector('.spinner-border').classList.remove('d-none');
      });
    }
  })();
</script>
@endsection
