@extends('layout.default')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{
    --primary:#175278;
    --accent:#FECA0A;
    --hover:#4A90B6;
    --text:#0f2533;
    --soft:#f7f8fa;
  }
  body{ background:var(--soft); }

  /* Buttons */
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-primary{ border-color:var(--primary); color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--hover); border-color:var(--hover); color:#fff; }

  .btn-accent{
    background:var(--accent); border-color:var(--accent);
    color:var(--text); font-weight:600;
  }
  .btn-accent:hover{ filter:brightness(0.97); color:var(--text); }

  /* Card & header */
  .shadow-soft{ box-shadow:0 10px 30px rgba(23,82,120,.08); }
  .page-header{ border-bottom:1px solid #e9ecef; margin-bottom:1rem; padding-bottom:.5rem; }
  .page-title{ color:var(--primary); }

  /* Inputs focus */
  .form-control:focus, .form-select:focus{
    border-color: var(--primary);
    box-shadow: 0 0 0 .18rem rgba(23,82,120,.15);
  }

  /* Switch éligible */
  .form-switch .form-check-input{ width:3rem; height:1.6rem; background-color:#dee2e6; border-color:#dee2e6; }
  .form-switch .form-check-input:checked{ background-color:var(--primary); border-color:var(--primary); }
  .hint{ font-size:.85rem; color:#6c757d; }

  /* Uppercase visuel pour le matricule (et code) */
  .uppercase{ text-transform: uppercase; letter-spacing:.2px; }
</style>
@endpush

@section('content')
<div class="container py-4">

  {{-- Header --}}
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-arrow-left"></i>
      </a>
      <h1 class="h4 mb-0 page-title">
        <i class="bi bi-person-plus-fill me-1"></i> Ajouter un employé
      </h1>
    </div>
  </div>

  {{-- Erreurs globales --}}
  @if($errors->any())
    <div class="alert alert-danger shadow-soft">
      <i class="bi bi-exclamation-triangle me-1"></i>
      Veuillez corriger les champs ci-dessous.
      <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- Formulaire --}}
  <form id="employeeForm" method="post" action="{{ route('employees.store') }}" class="card shadow-soft border-0">
    @csrf
    <div class="card-header bg-white">
      <div class="d-flex align-items-center justify-content-between">
        <span class="fw-semibold" style="color:var(--primary)">Informations de l’employé</span>
        <span class="badge rounded-pill" style="background:rgba(254,202,10,.2); color:#7a5a00;">
          <i class="bi bi-shield-check me-1"></i> Données internes
        </span>
      </div>
    </div>

    <div class="card-body">
      {{-- Ligne: Identifiants internes --}}
      <div class="row g-3 mb-2">
        <div class="col-md-6">
          <label class="form-label">Matricule <span class="text-danger">*</span></label>
          <input id="matriculeInput" name="matricule"
                 class="form-control uppercase @error('matricule') is-invalid @enderror"
                 value="{{ old('matricule') }}" placeholder="Ex: EMP-001" required>
          @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="hint mt-1">Caractères autorisés : lettres, chiffres, tiret (-) et underscore (_).</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Code employé (optionnel)</label>
          <input id="employeeCodeInput" name="employee_code"
                 class="form-control uppercase @error('employee_code') is-invalid @enderror"
                 value="{{ old('employee_code') }}" placeholder="Ex: PMG-2025-07">
          @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Ligne: état civil --}}
      <div class="row g-3 mb-2">
        <div class="col-md-6">
          <label class="form-label">Prénom <span class="text-danger">*</span></label>
          <input name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                 value="{{ old('first_name') }}" placeholder="Ex: Aïcha" required>
          @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Nom <span class="text-danger">*</span></label>
          <input name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                 value="{{ old('last_name') }}" placeholder="Ex: OUEDRAOGO" required>
          @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Ligne: contacts --}}
      <div class="row g-3 mb-2">
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" placeholder="exemple@domaine.com">
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Téléphone</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input id="phoneInput" name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}" placeholder="Ex: 70 00 00 00">
            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div id="phoneHelp" class="hint mt-1">8 chiffres (format BF). Les espaces se mettent automatiquement.</div>
        </div>
      </div>

      {{-- Ligne: salaire + éligible --}}
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Salaire mensuel <span class="text-danger">*</span></label>
          <div class="input-group">
            <input id="salaryInput" type="number" min="1" name="monthly_salary"
                   class="form-control @error('monthly_salary') is-invalid @enderror"
                   value="{{ old('monthly_salary') }}" placeholder="Ex: 200000" required>
            <span class="input-group-text">FCFA</span>
            @error('monthly_salary')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="hint mt-1">Le plafond d’avance sera calculé = <strong>salaire / 4</strong>.</div>
        </div>
        <div class="col-md-6 d-flex align-items-center">
          <div class="form-check form-switch ms-1">
            <input type="hidden" name="eligible" value="0">
            <input type="checkbox" name="eligible" value="1"
                   class="form-check-input @error('eligible') is-invalid @enderror"
                   id="eligibleSwitch" {{ old('eligible') ? 'checked' : '' }}>
            <label for="eligibleSwitch" class="form-check-label ms-2 fw-semibold" style="color:var(--primary)">
              Éligible à l’avance
            </label>
            @error('eligible')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <div class="hint">Si activé, les demandes pourront être auto-approuvées (MVP).</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="card-footer bg-white d-flex flex-wrap gap-2">
      <button class="btn btn-primary">
        <i class="bi bi-save2 me-1"></i> Enregistrer
      </button>
      <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-x-lg me-1"></i> Annuler
      </a>
      <button type="reset" class="btn btn-accent ms-auto">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
      </button>
    </div>
  </form>
</div>

{{-- JS inline pour pré-validation (léger, sans dépendances) --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const matricule = document.getElementById('matriculeInput');
    const employeeCode = document.getElementById('employeeCodeInput');
    const phone = document.getElementById('phoneInput');
    const salary = document.getElementById('salaryInput');
    const form = document.getElementById('employeeForm');

    // Helper: garde lettres/chiffres/underscore/tiret, uppercase
    function sanitizeUpperId(val){
      return (val || '')
        .toUpperCase()
        .replace(/\s+/g, '')            // retire espaces
        .replace(/[^A-Z0-9_-]/g, '');   // autorisés: A-Z 0-9 _ -
    }

    // Matricule & Code employé: uppercase + contraintes
    if (matricule) {
      matricule.addEventListener('input', () => {
        const pos = matricule.selectionStart;
        matricule.value = sanitizeUpperId(matricule.value);
        matricule.setSelectionRange(pos, pos);
      });
    }
    if (employeeCode) {
      employeeCode.addEventListener('input', () => {
        const pos = employeeCode.selectionStart;
        employeeCode.value = sanitizeUpperId(employeeCode.value);
        employeeCode.setSelectionRange(pos, pos);
      });
    }

    // Téléphone: format BF "XX XX XX XX", accepte saisie +226/00226 et coupe le préfixe
    function formatPhone(val){
      if (!val) return '';
      let digits = val.replace(/\D/g, '');

      // Enlève préfixes internationaux
      if (digits.startsWith('00226')) digits = digits.slice(5);
      else if (digits.startsWith('226')) digits = digits.slice(3);

      digits = digits.slice(0, 8); // 8 chiffres max (BF)
      const parts = [];
      for (let i = 0; i < digits.length; i += 2) {
        parts.push(digits.substring(i, i + 2));
      }
      return parts.join(' ').trim();
    }

    function isPhoneValid(val){
      const digits = val.replace(/\D/g, '');
      return digits.length === 8; // 8 chiffres BF
    }

    if (phone) {
      phone.addEventListener('input', () => {
        const start = phone.selectionStart;
        const before = phone.value;
        phone.value = formatPhone(phone.value);

        // ajuste le caret grossièrement
        const delta = phone.value.length - before.length;
        const newPos = Math.max(0, (start || 0) + delta);
        phone.setSelectionRange(newPos, newPos);

        phone.classList.toggle('is-invalid', !isPhoneValid(phone.value) && phone.value.length > 0);
        phone.classList.toggle('is-valid', isPhoneValid(phone.value));
      });

      phone.addEventListener('blur', () => {
        phone.classList.toggle('is-invalid', !isPhoneValid(phone.value) && phone.value.length > 0);
        phone.classList.toggle('is-valid', isPhoneValid(phone.value));
      });
    }

    // Salaire: empêche valeurs négatives / non numériques (type="number" gère déjà en partie)
    if (salary) {
      salary.addEventListener('input', () => {
        if (salary.value && Number(salary.value) < 0) salary.value = '';
      });
    }

    // Avant submit: normalise certaines valeurs
    form?.addEventListener('submit', (e) => {
      // Téléphone: enlever espaces
      if (phone) {
        const digits = phone.value.replace(/\D/g, '');
        if (digits.length !== 8) {
          phone.classList.add('is-invalid');
          e.preventDefault();
          return;
        }
        phone.value = digits; // envoie 8 chiffres au backend
      }

      // Matricule/code: assure uppercase nettoyé
      if (matricule) matricule.value = sanitizeUpperId(matricule.value);
      if (employeeCode) employeeCode.value = sanitizeUpperId(employeeCode.value);
    });
  });
</script>
@endsection
