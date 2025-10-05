@extends('layout.empty')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body {
    background:
      linear-gradient(rgba(18,20,20,.65), rgba(11,16,19,.8)),
      url('{{ asset('assets/img/bg2.jpg') }}') no-repeat center/cover fixed !important;
    min-height:100vh; margin:0;
    display:flex; align-items:center; justify-content:center;
    font-family: 'Segoe UI', system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif;
  }
  .auth-card {
    width:100%; max-width:560px;
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(10px);
    border-radius:1.2rem;
    border:1px solid #e0e0e0;
  }
  h4 { color:#175278; font-weight:600; }
  .btn-primary { background-color:#175278; border:none; font-weight:600; letter-spacing:.3px; transition:all .3s ease; }
  .btn-primary:hover { background-color:#4A90B6; }
  .btn-outline-secondary { border-color:#dee2e6; color:#175278; transition:all .3s ease; }
  .btn-outline-secondary:hover { border-color:#4A90B6; color:#4A90B6; background:#f5f5f5; }
  .form-control:focus { border-color:#4A90B6; box-shadow:0 0 0 .2rem rgba(74,144,182,.25); }
  .input-group-text { background:#f8f9fa; color:#175278; }
  .btn-icon {
    width:2.5rem; height:2.5rem; padding:0;
    display:inline-flex; align-items:center; justify-content:center;
    color:#175278; border-color:#dee2e6;
  }
  .btn-icon:hover { color:#4A90B6; border-color:#4A90B6; background:#f8f9fa; }
  .alert-danger { background:#fff0f0; color:#c0392b; border:none; border-radius:.6rem; }
  .help { color:#6c757d; font-size:.875rem; }
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
          <h4 class="mb-1">Créer un compte entreprise</h4>
          <p class="text-muted small mb-0">Renseignez les informations de votre société</p>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('company.register') }}" novalidate>
          @csrf

          <div class="row g-3">
            {{-- Raison sociale --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Raison sociale</label>
              <input type="text" name="name" value="{{ old('name') }}"
                     class="form-control @error('name') is-invalid @enderror" required>
              @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            {{-- RCCM / IFU --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">RCCM</label>
              <input type="text" name="rccm" value="{{ old('rccm') }}"
                     class="form-control @error('rccm') is-invalid @enderror">
              @error('rccm') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">IFU</label>
              <input type="text" name="ifu" value="{{ old('ifu') }}"
                     class="form-control @error('ifu') is-invalid @enderror">
              @error('ifu') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            {{-- Email / Téléphone --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" value="{{ old('email') }}"
                     class="form-control @error('email') is-invalid @enderror" required>
              @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Téléphone</label>
              <input type="text" name="phone" value="{{ old('phone') }}"
                     class="form-control @error('phone') is-invalid @enderror" required>
              @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            {{-- Adresse --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Adresse (optionnel)</label>
              <input type="text" name="address" value="{{ old('address') }}"
                     class="form-control @error('address') is-invalid @enderror">
              @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              <div class="help mt-1">Ex. quartier, rue, ville…</div>
            </div>

            {{-- Mot de passe / Confirmation --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Mot de passe</label>
              <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                <button class="btn btn-icon" type="button" id="togglePwd1" aria-label="Afficher/masquer">
                  <i class="bi bi-eye" id="eye1"></i>
                  <i class="bi bi-eye-slash d-none" id="eye1off"></i>
                </button>
              </div>
              @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              <div class="help mt-1">8 caractères minimum.</div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Confirmer le mot de passe</label>
              <div class="input-group">
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                <button class="btn btn-icon" type="button" id="togglePwd2" aria-label="Afficher/masquer">
                  <i class="bi bi-eye" id="eye2"></i>
                  <i class="bi bi-eye-slash d-none" id="eye2off"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="d-grid d-sm-flex justify-content-sm-between align-items-center gap-3 mt-4">
            <a href="{{ route('company.login') }}" class="btn btn-outline-secondary w-100 w-sm-auto">Déjà inscrit ? Se connecter</a>
            <button type="submit" class="btn btn-primary btn-lg w-100 w-sm-auto">Créer mon compte</button>
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
  const toggle = (btnId, inputId, eyeOnId, eyeOffId) => {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    const eyeOn = document.getElementById(eyeOnId);
    const eyeOff = document.getElementById(eyeOffId);
    if (btn && input) {
      btn.addEventListener('click', () => {
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        eyeOn.classList.toggle('d-none', isPwd);
        eyeOff.classList.toggle('d-none', !isPwd);
      });
    }
  };
  toggle('togglePwd1','password','eye1','eye1off');
  toggle('togglePwd2','password_confirmation','eye2','eye2off');
});
</script>
@endpush
