@extends('layout.empty')

@push('css')
<style>
  body {
    background:
      linear-gradient(rgba(18, 20, 20, 0.65), rgba(11, 16, 19, 0.8)),
      url('{{ asset('assets/img/bg2.jpg') }}') no-repeat center/cover fixed !important;
    min-height:100vh; margin:0;
    display:flex; align-items:center; justify-content:center;
    font-family: 'Segoe UI', sans-serif;
  }

  .auth-card {
    width:100%; max-width:460px;
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(10px);
    border-radius:1.2rem;
    border:1px solid #e0e0e0;
  }

  /* Titres */
  h4 {
    color:#175278;
    font-weight:600;
  }

  /* Bouton principal */
  .btn-primary {
    background-color:#175278;
    border:none;
    font-weight:600;
    letter-spacing:.3px;
    transition:all .3s ease;
  }
  .btn-primary:hover {
    background-color:#4A90B6;
  }

  /* Liens */
  a {
    color:#175278;
    transition:color .3s ease;
  }
  a:hover {
    color:#4A90B6;
  }

  /* Input group */
  .input-group-text {
    background-color:#f8f9fa;
    border:1px solid #dee2e6;
    color:#175278;
  }
  .form-control:focus {
    border-color:#4A90B6;
    box-shadow:0 0 0 .2rem rgba(74,144,182,.25);
  }

  /* Switch "Se souvenir de moi" */
  .form-check-input:checked {
    background-color:#FECA0A;
    border-color:#FECA0A;
  }

  /* Icon toggle btn */
  .btn-icon {
    width:2.5rem; height:2.5rem; padding:0;
    display:inline-flex; align-items:center; justify-content:center;
    border-color:#dee2e6;
    color:#175278;
  }
  .btn-icon:hover {
    background-color:#f5f5f5;
    border-color:#4A90B6;
    color:#4A90B6;
  }

  /* Alertes */
  .alert-success {
    background:#e9f9ef; color:#175278; border:none;
  }
  .alert-danger {
    background:#fff0f0; color:#c0392b; border:none;
  }
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-center align-items-center">
    <div class="card shadow-lg border-0 auth-card">
      <div class="card-body p-4 p-md-5">

        <div class="text-center mb-4">
            <div class="mb-3">
              <img src="{{ asset('assets/img/logo.png') }}" alt="Logo entreprise" height="110" class="mb-2">
            </div>
            <h4 class="mb-1">Connexion entreprise</h4>
            <p class="text-muted small mb-0">Accédez à votre espace société</p>
        </div>


        @if (session('ok'))
          <div class="alert alert-success mb-3">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('company.login') }}" novalidate>
          @csrf

          <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input id="email" type="email" name="email" value="{{ old('email') }}"
                     class="form-control @error('email') is-invalid @enderror"
                     placeholder="vous@entreprise.com" required autofocus>
            </div>
            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Mot de passe</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input id="password" type="password" name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="••••••••" required>
              <button class="btn btn-icon" type="button" id="togglePassword">
                <i class="bi bi-eye" id="eyeOpen"></i>
                <i class="bi bi-eye-slash d-none" id="eyeClosed"></i>
              </button>
            </div>
            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
              <label class="form-check-label small" for="remember_me">Se souvenir de moi</label>
            </div>
            <div class="d-flex gap-3">
              @if (Route::has('company.register'))
                <a class="fw-semibold text-decoration-none" href="{{ route('company.register') }}">S’inscrire</a>
              @endif
            </div>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('togglePassword');
  const input = document.getElementById('password');
  const eyeOpen = document.getElementById('eyeOpen');
  const eyeClosed = document.getElementById('eyeClosed');
  if (btn && input) {
    btn.addEventListener('click', () => {
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      eyeOpen.classList.toggle('d-none', isPwd);
      eyeClosed.classList.toggle('d-none', !isPwd);
    });
  }
});
</script>
@endpush
