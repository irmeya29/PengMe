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

  /* Buttons */
  .btn-primary{ background:var(--primary); border-color:var(--primary); }
  .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  .btn-outline-primary{ border-color:var(--primary); color:var(--primary); }
  .btn-outline-primary:hover{ background:var(--hover); border-color:var(--hover); color:#fff; }
  .btn-accent{ background:var(--accent); border-color:var(--accent); color:var(--text); font-weight:600; }
  .btn-accent:hover{ filter:brightness(0.97); color:var(--text); }

  /* Cards & header */
  .shadow-soft{ box-shadow:0 10px 30px rgba(23,82,120,.08); }
  .page-title{ color:var(--primary); }

  /* Inputs focus */
  .form-control:focus, .form-select:focus{
    border-color: var(--primary);
    box-shadow: 0 0 0 .18rem rgba(23,82,120,.15);
  }

  /* Dropzone */
  .dropzone{
    border:2px dashed rgba(23,82,120,.35);
    border-radius:16px;
    background:#fff;
    transition:.2s ease;
  }
  .dropzone:hover{ border-color:var(--hover); }
  .dropzone.dragover{
    border-color:var(--primary);
    background:rgba(23,82,120,.03);
  }

  /* Code / pre */
  pre.sample{ background:#fff; border:1px solid #e9ecef; border-radius:12px; }
  code{ color:#0b5ed7; }
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">
      <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="h4 page-title mb-0">📂 Importer des employés (CSV)</h1>
  </div>

  {{-- Flashs --}}
  @if(session('ok'))
    <div class="alert alert-success shadow-soft"><i class="bi bi-check2-circle me-1"></i> {{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger shadow-soft">
      <i class="bi bi-exclamation-triangle me-1"></i> Erreurs détectées.
      <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card shadow-soft border-0">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0" style="color:var(--primary)"><i class="bi bi-info-circle me-1"></i> Instructions d’import</h5>
        <div class="d-flex gap-2">
          <button id="downloadTemplate" type="button" class="btn btn-accent">
            <i class="bi bi-filetype-csv me-1"></i> Télécharger le modèle CSV
          </button>
        </div>
      </div>

      <p class="text-muted mb-2">
        Le fichier CSV doit contenir une ligne d’en-tête avec les colonnes suivantes :
      </p>

      <pre class="sample p-3 mb-3"><code>matricule,first_name,last_name,email,phone,monthly_salary,employee_code,eligible</code></pre>

      <ul class="small text-muted mb-4">
        <li><code>eligible</code> accepte <code>1</code>/<code>0</code>, <code>oui</code>/<code>non</code>, ou <code>true</code>/<code>false</code>.</li>
        <li>Si un <strong>matricule</strong> existe déjà pour l’entreprise, l’employé sera <strong>mis à jour</strong>.</li>
        <li>Le <strong>mot de passe n’est pas importé</strong> : l’employé le définira lors de sa première connexion.</li>
        <li>Délimiteur conseillé : <strong>virgule</strong> (,) — encadrez les champs contenant des virgules avec des guillemets.</li>
      </ul>

      {{-- Formulaire / Dropzone --}}
      <form id="csvForm" method="post" action="{{ route('employees.import') }}" enctype="multipart/form-data">
        @csrf

        <div id="dropzone" class="dropzone p-4 mb-3 d-flex flex-column align-items-center text-center">
          <div class="mb-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                  style="width:48px;height:48px;background:rgba(254,202,10,.35); color:#7a5a00;">
              <i class="bi bi-upload"></i>
            </span>
          </div>
          <div class="mb-2 fw-semibold">Glissez-déposez votre fichier CSV ici</div>
          <div class="text-muted small mb-3">ou</div>

          <div class="d-flex gap-2 flex-wrap justify-content-center">
            <label class="btn btn-primary">
              <i class="bi bi-folder2-open me-1"></i> Choisir un fichier
              <input id="csvInput" type="file" name="file" accept=".csv,text/csv" hidden required>
            </label>
            <span id="fileName" class="align-self-center small text-muted"></span>
          </div>
          <div class="small text-muted mt-2">Formats acceptés : .csv (max ~5–10 Mo conseillé)</div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-download me-1"></i> Importer
          </button>
          <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-x-lg me-1"></i> Annuler
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- JS : dropzone + vérifs + génération modèle CSV --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const dz = document.getElementById('dropzone');
    const input = document.getElementById('csvInput');
    const fileName = document.getElementById('fileName');
    const form = document.getElementById('csvForm');
    const downloadBtn = document.getElementById('downloadTemplate');

    // Affiche le nom du fichier choisi
    function showFileName(file){
      fileName.textContent = file ? file.name : '';
    }

    // Validation simple: extension csv
    function isCsv(file){
      if (!file) return false;
      const name = (file.name || '').toLowerCase();
      return name.endsWith('.csv') || file.type === 'text/csv';
    }

    input.addEventListener('change', () => {
      const file = input.files?.[0];
      showFileName(file);
      if (file && !isCsv(file)) {
        alert('Veuillez sélectionner un fichier .csv valide.');
        input.value = '';
        showFileName(null);
      }
    });

    // Drag & Drop
    ;['dragenter','dragover','dragleave','drop'].forEach(evt => {
      dz.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); }, false);
    });
    ;['dragenter','dragover'].forEach(evt => {
      dz.addEventListener(evt, () => dz.classList.add('dragover'), false);
    });
    ;['dragleave','drop'].forEach(evt => {
      dz.addEventListener(evt, () => dz.classList.remove('dragover'), false);
    });
    dz.addEventListener('drop', e => {
      const file = e.dataTransfer.files?.[0];
      if (!file) return;
      if (!isCsv(file)) {
        alert('Fichier invalide. Sélectionnez un .csv');
        return;
      }
      input.files = e.dataTransfer.files; // assigne au vrai input
      showFileName(file);
    });

    // Modèle CSV à télécharger (généré côté client)
    downloadBtn.addEventListener('click', () => {
      const header = 'matricule,first_name,last_name,email,phone,monthly_salary,employee_code,eligible\n';
      const sample = [
        'EMP-001,Jean,Traoré,jean.traore@exemple.com,70000000,250000,INT-01,oui',
        'EMP-002,Aïcha,Ouédraogo,aicha.ouedraogo@exemple.com,71000000,180000,,non',
      ].join('\n');
      const csv = header + sample + '\n';
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'modele_employes.csv';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    });

    // Vérification avant submit
    form.addEventListener('submit', (e) => {
      const file = input.files?.[0];
      if (!file || !isCsv(file)) {
        e.preventDefault();
        alert('Veuillez choisir un fichier .csv valide avant de lancer l’import.');
      }
    });
  });
</script>
@endsection
