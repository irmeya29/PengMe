<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name', 'PengMe') }} — Avance sur Salaire simplifiée</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary:#175278;
      --primary-light:#1d6b8f;
      --accent:#FECA0A;
    }

    body {
      font-family:'Poppins',sans-serif;
      background:#fdfdfd;
      color:#0f172a;
    }

    /* NAV */
    .navbar {
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,.9);
      border-bottom: 1px solid rgba(23,82,120,.15);
    }
    .logo { height:56px; width:auto; }
    .btn-login {
      background: var(--primary); color:#fff; font-weight:600; border:none; border-radius:12px;
      padding:.6rem 1.1rem; box-shadow:0 6px 14px rgba(23,82,120,.25);
    }
    .btn-login:hover { background: var(--primary-light); }

    /* HERO */
    .hero {
      position:relative;
      min-height:86vh;
      display:flex;
      align-items:center;
      background:url('{{ asset('assets/images/bg-hero.jpg') }}') center/cover no-repeat;
      color:#fff;
      padding:64px 0;
    }
    .hero::before {
      content:"";
      position:absolute; inset:0;
      background:rgba(0,0,0,0.55);
    }
    .hero .container { position:relative; z-index:2; }
    .hero h1 {
      font-weight:800;
      font-size:clamp(2.2rem,1.6rem + 2.8vw,3.2rem);
      line-height:1.1;
    }
    .hero p.lead {
      color:#eaf3fa;
      font-size:clamp(1rem,.95rem + .4vw,1.2rem);
      max-width:640px;
    }

    /* BUTTONS */
    .btn-primary-brand {
      background: var(--accent);
      border:none;
      border-radius:12px;
      color:#16222b;
      font-weight:700;
      padding:.85rem 1.4rem;
      box-shadow:0 10px 22px rgba(254,202,10,.3);
      transition: all .3s ease;
    }
    .btn-primary-brand:hover {
      background: var(--primary);
      color:#fff;
      box-shadow:0 8px 18px rgba(23,82,120,.25);
    }

    .btn-outline-brand {
      border:1px solid #fff;
      color:#fff;
      border-radius:12px;
      font-weight:600;
      padding:.85rem 1.4rem;
      transition: all .3s ease;
    }
    .btn-outline-brand:hover { background:#fff; color:var(--primary); }

    /* GLASS CARD */
    .glass-effect {
      border-radius:20px;
      padding:24px;
      background:rgba(255,255,255,0.12);
      border:1px solid rgba(255,255,255,0.25);
      backdrop-filter:blur(14px);
      box-shadow:0 12px 30px rgba(23,82,120,.25);
      color:#fff;
    }
    .glass-effect .eyebrow {
      display:inline-flex;align-items:center;gap:.5rem;
      padding:.35rem .7rem;border-radius:999px;
      background:rgba(254,202,10,0.15);
      color:#FECA0A;font-weight:600;font-size:.9rem;
    }
    .glass-effect h5{font-weight:700;font-size:1.2rem;margin:1rem 0 .8rem;}
    .list-adv{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.9rem;}
    .list-adv li{display:flex;gap:.8rem;align-items:flex-start;background:rgba(255,255,255,0.07);
      border-radius:14px;padding:.8rem .9rem;border:1px solid rgba(255,255,255,0.12);
      transition:background .2s ease;}
    .list-adv li:hover{background:rgba(255,255,255,0.18);}
    .list-adv .ico{flex:0 0 38px;height:38px;display:grid;place-items:center;
      background:rgba(254,202,10,0.1);border-radius:12px;color:#FECA0A;font-size:1.1rem;}
    .list-adv .title{font-weight:600;color:#fff;}
    .list-adv .desc{font-size:.9rem;color:rgba(255,255,255,0.85);}
    .chips{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1.1rem;}
    .chip{background:rgba(254,202,10,0.15);border:1px solid rgba(254,202,10,0.3);
      color:#FECA0A;padding:.35rem .7rem;border-radius:999px;font-size:.8rem;font-weight:600;}

    /* APP SECTION */
    .section-app{background:#f7fafc;}
    .phone-frame{background:#fff;border:1px solid rgba(23,82,120,.15);border-radius:28px;padding:10px;
      box-shadow:0 16px 36px rgba(23,82,120,.15);}
    .phone-frame img{border-radius:22px;width:100%;height:auto;display:block;}
    .store-badge img{height:50px;}

    /* CTA CONTACT */
    .btn-primary-brand:hover i { color:#fff; }
    footer{color:#6b7f91;background:#fff;}
  </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
      <img class="logo" src="{{ asset('assets/images/logo.png') }}" alt="PengMe">
      <span class="fw-bold" style="color:#17384d;">PengMe</span>
    </a>
    <div class="ms-auto">
      <a class="btn btn-login" href="{{ route('login') }}">
        <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
      </a>
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="hero">
  <div class="container">
    <div class="row align-items-center gy-5">
      <div class="col-lg-7">
        <h1>Avance sur salaire <span style="color:#FECA0A;">simple</span>, <span style="color:#FECA0A;">rapide</span> et <span style="color:#FECA0A;">sécurisée</span>.</h1>
        <p class="lead mt-3">PengMe aide les entreprises à offrir une avance salariale responsable, tout en simplifiant la gestion côté RH et Comptabilité.</p>

        <div class="d-flex flex-wrap gap-3 mt-4">
          <a class="btn btn-primary-brand" href="{{ route('company.register') }}">
            <i class="bi bi-person-plus me-1"></i> S’inscrire
          </a>
          <a class="btn btn-outline-brand" href="{{ route('login') }}">
            Déjà client ? Se connecter
          </a>
        </div>
      </div>

      <!-- Glass Card -->
      <div class="col-lg-5">
        <div class="glass-effect">
          <div class="eyebrow"><i class="bi bi-stars"></i> Nouveautés</div>
          <h5>Ce qui change avec PengMe</h5>
          <ul class="list-adv">
            <li><span class="ico"><i class="bi bi-speedometer2"></i></span>
              <div><div class="title">Traitement instantané</div><div class="desc">Vos demandes d’avance sont validées en quelques minutes.</div></div></li>
            <li><span class="ico"><i class="bi bi-person-check"></i></span>
              <div><div class="title">Espace salarié clair</div><div class="desc">Suivez vos avances et leurs statuts en temps réel.</div></div></li>
            <li><span class="ico"><i class="bi bi-building-gear"></i></span>
              <div><div class="title">Outils RH efficaces</div><div class="desc">Validation rapide et exports compatibles avec votre paie.</div></div></li>
          </ul>
          <div class="chips">
            <span class="chip">Sécurisé</span><span class="chip">Rapide</span><span class="chip">Fiable</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- APP SECTION -->
<section class="section-app py-5">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <h3 class="fw-bold mb-3" style="color:#175278;">PengMe sur mobile</h3>
        <p class="text-secondary mb-4">
          Demandez une avance, suivez son statut et recevez vos notifications — le tout dans une app simple et sécurisée.
        </p>
        <a href="#" class="store-badge">
          <img src="{{ asset('assets/images/google-play-badge.svg') }}" alt="Disponible sur Google Play">
        </a>
      </div>

      <div class="col-lg-6">
        <div id="appCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
              <div class="phone-frame mx-auto" style="max-width:300px;">
                <img src="{{ asset('assets/images/app-1.jpeg') }}" alt="Capture PengMe 1">
              </div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
              <div class="phone-frame mx-auto" style="max-width:300px;">
                <img src="{{ asset('assets/images/app-2.jpeg') }}" alt="Capture PengMe 2">
              </div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
              <div class="phone-frame mx-auto" style="max-width:300px;">
                <img src="{{ asset('assets/images/app-3.jpeg') }}" alt="Capture PengMe 3">
              </div>
            </div>
          </div>

          <!-- Indicateurs -->
          <div class="carousel-indicators mt-3">
            <button type="button" data-bs-target="#appCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#appCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#appCarousel" data-bs-slide-to="2"></button>
          </div>

          <!-- Flèches -->
          <button class="carousel-control-prev" type="button" data-bs-target="#appCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Précédent</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#appCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Suivant</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT CTA -->
<section class="py-5 text-center">
  <div class="container">
    <h3 class="fw-bold mb-2" style="color:#175278;">Besoin d’informations ?</h3>
    <p class="text-muted mb-4">Notre équipe est disponible pour répondre à vos questions ou planifier une démonstration.</p>
    <a class="btn btn-primary-brand px-4" href="mailto:support@pengme.net">
      <i class="bi bi-envelope me-1"></i> Nous contacter
    </a>
  </div>
</section>

<!-- FOOTER -->
<footer class="py-4 border-top">
  <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
    <div class="d-flex align-items-center gap-2">
      <img class="logo" src="{{ asset('assets/images/logo.png') }}" alt="PengMe">
      <span class="fw-semibold">PengMe</span>
      <span class="text-secondary">© {{ now()->year }} Tous droits réservés.</span>
    </div>
    <div>
      <a class="text-decoration-none" href="mailto:support@pengme.net">
        <i class="bi bi-envelope me-1"></i> support@pengme.net
      </a>
    </div>
  </div>
</footer>

<!-- JS Bootstrap + activation carousel -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  const carousel=document.querySelector('#appCarousel');
  if(carousel){
    new bootstrap.Carousel(carousel,{interval:4000,ride:'carousel',pause:false,wrap:true});
  }
});
</script>
</body>
</html>
