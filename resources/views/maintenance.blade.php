<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <title>Maintenance — {{ config('app.name', 'PengMe') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap 5 + Icons + Poppins (même stack que ton index.html) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --primary:#175278;
      --primary-hover:#1d668d;
      --accent:#FECA0A;
    }

    html,body{height:100%;}
    body{
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color:#fff;
      /* Fond proche de ton index: dégradé bleu + halos doux */
      background:
        radial-gradient(1100px 520px at 100% 0, rgba(255, 216, 64, 0.10), transparent 60%),
        radial-gradient(900px 520px at 0 100%, rgba(23,82,120,.18), transparent 60%),
        linear-gradient(135deg, #0b1f2d, #175278);
    }

    .wrap{
      min-height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px 16px;
    }

    .card-hero{
      width:100%;
      max-width: 920px;
      border-radius: 24px;
      background: rgba(255,255,255,.06);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.15);
      box-shadow: 0 12px 28px rgba(0,0,0,.35);
      padding: 2.5rem;
    }

    .brand{display:flex; align-items:center; gap:12px;}
    .brand img{height:56px; width:auto; filter: drop-shadow(0 2px 6px rgba(0,0,0,.3));}
    .brand .title{font-weight:600; font-size:1.2rem;}

    .headline{ font-weight:700; font-size:2.1rem; margin:1rem 0 .5rem; }
    .subhead{ color:#dbe6f0; margin-bottom:1.5rem; }

    /* Timer */
    .timer{display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:2rem;}
    @media (max-width: 540px){ .timer{grid-template-columns: repeat(2, 1fr);} }
    .time-box{
      background: rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.15);
      border-radius:16px;
      padding:18px; text-align:center; position:relative; overflow:hidden;
    }
    .time-box .value{ font-size:2rem; font-weight:700; margin-bottom:.3rem; }
    .time-box .label{ font-size:.85rem; text-transform:uppercase; color:#e8eef5; }
    .time-box .bar{ position:absolute; left:0; bottom:0; height:3px; width:0%; background: linear-gradient(90deg, var(--accent), #fff); transition: width .35s ease; }

    /* Bouton */
    .btn-accent{
      background: var(--primary);
      color:#fff;
      font-weight:600;
      border:none;
      border-radius: 12px;
      padding:.7rem 1.5rem;
      box-shadow: 0 6px 14px rgba(23,82,120,.3);
      transition: background .2s ease, transform .15s ease, filter .15s ease;
    }
    .btn-accent:hover{ background: var(--primary-hover); transform: translateY(-2px); filter: brightness(.98); }

    .info-card{
      background: rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.12);
      border-radius:16px;
      padding:1.5rem;
    }
    .info-card ul{list-style:none; padding:0; margin:0;}
    .info-card li{
      margin-bottom: .75rem;
      display:flex; align-items:center; gap:.6rem;
    }
    .info-card li i{font-size:1.2rem; color:var(--accent);}
    .info-card li span{flex:1;}

    .footer-mini{color:#cfe6ff; text-align:center; margin-top:2rem; font-size:.9rem;}
  </style>
</head>
<body>

<main class="wrap">
  <section class="card-hero">

    {{-- Ligne logo + badge --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
      <div class="brand">
        <img src="{{ asset('assets/img/logo1.png') }}" alt="{{ config('app.name', 'PengMe') }}">
        <div class="title">Maintenance programmée</div>
      </div>
      <div class="text-end mt-3 mt-md-0">
        <span class="badge rounded-pill text-dark px-3 py-2" style="background:#FECA0A; font-weight:600;">
          Retour prévu : Lun 06 Oct 2025, 09:00
        </span>
      </div>
    </div>

    <div class="row g-4">
      {{-- Col gauche --}}
      <div class="col-lg-7">
        <h1 class="headline">Nous revenons très vite.</h1>
        <p class="subhead">Nous améliorons l’expérience et déployons de nouvelles fonctionnalités. Merci pour votre patience.</p>

        {{-- Compte à rebours --}}
        <div class="timer" id="countdown">
          <div class="time-box">
            <div class="value" id="days">0</div>
            <div class="label">Jours</div>
            <span class="bar" id="bar-days"></span>
          </div>
          <div class="time-box">
            <div class="value" id="hours">0</div>
            <div class="label">Heures</div>
            <span class="bar" id="bar-hours"></span>
          </div>
          <div class="time-box">
            <div class="value" id="minutes">0</div>
            <div class="label">Minutes</div>
            <span class="bar" id="bar-minutes"></span>
          </div>
          <div class="time-box">
            <div class="value" id="seconds">0</div>
            <div class="label">Secondes</div>
            <span class="bar" id="bar-seconds"></span>
          </div>
        </div>

        <a class="btn btn-accent" href="mailto:support@pengme.net">Nous contacter</a>
      </div>

      {{-- Col droite --}}
      <div class="col-lg-5">
        <div class="info-card">
          <h5 class="fw-bold mb-3">Détails de la maintenance</h5>
          <ul>
            <li><i class="bi bi-hdd-network"></i><span>Mise en ligne des services backend</span></li>
            <li><i class="bi bi-phone"></i><span>Optimisations API pour l’app mobile</span></li>
            <li><i class="bi bi-shield-lock"></i><span>Améliorations sécurité & stabilité</span></li>
          </ul>
          <div class="small mt-3">
            Fenêtre prévue : <strong>Lundi 6 octobre 2025</strong> — <strong>09:00 (UTC)</strong>.<br>
            Statut en temps réel : <em>compte à rebours actif</em>.
          </div>
        </div>
      </div>
    </div>

    {{-- Footer --}}
    <div class="footer-mini">© {{ now()->year }} {{ config('app.name', 'PengMe') }}. Tous droits réservés.</div>
  </section>
</main>

<script>
  // Cible UTC : 2025-10-06 09:00:00
  const target = new Date('2025-10-06T09:00:00Z').getTime();
  const elDays=document.getElementById('days'),
        elHours=document.getElementById('hours'),
        elMinutes=document.getElementById('minutes'),
        elSeconds=document.getElementById('seconds');
  const barDays=document.getElementById('bar-days'),
        barHours=document.getElementById('bar-hours'),
        barMinutes=document.getElementById('bar-minutes'),
        barSeconds=document.getElementById('bar-seconds');

  function clamp(v){ return Math.max(0, Math.min(100, v)); }

  function updateCountdown(){
    const now=Date.now();
    let delta=target-now;

    if(delta<=0){
      elDays.textContent=elHours.textContent=elMinutes.textContent=elSeconds.textContent='0';
      [barDays,barHours,barMinutes,barSeconds].forEach(b=>b.style.width='100%');
      return;
    }

    const second=1000, minute=60*second, hour=60*minute, day=24*hour;
    const d=Math.floor(delta/day); delta-=d*day;
    const h=Math.floor(delta/hour); delta-=h*hour;
    const m=Math.floor(delta/minute); delta-=m*minute;
    const s=Math.floor(delta/second);

    elDays.textContent=d;
    elHours.textContent=String(h).padStart(2,'0');
    elMinutes.textContent=String(m).padStart(2,'0');
    elSeconds.textContent=String(s).padStart(2,'0');

    // Barres de progression “vivantes”
    const nowDate=new Date();
    const msStartDay=nowDate - new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate());
    barDays.style.width    = clamp(msStartDay/(24*60*60*1000)*100) + '%';
    barHours.style.width   = clamp(((nowDate.getMinutes()*60)+nowDate.getSeconds())/3600*100) + '%';
    barMinutes.style.width = clamp(nowDate.getSeconds()/60*100) + '%';
    barSeconds.style.width = clamp(nowDate.getMilliseconds()/1000*100) + '%';
  }

  // refresh “fluide”
  const interval = setInterval(updateCountdown, 200);
  updateCountdown();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
