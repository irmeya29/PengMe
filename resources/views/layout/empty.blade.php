<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'PengMe')</title>

  {{-- Bootstrap + Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Palette projet --}}
  <style>
    :root { --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; --soft:#f7f9fb; }
    html,body{ height:100%; background: linear-gradient(180deg,#f8fbff 0%,#f3f7fb 100%); }
    .btn-primary{ background:var(--primary); border-color:var(--primary); }
    .btn-primary:hover{ background:var(--hover); border-color:var(--hover); }
  </style>

  @stack('css')
</head>
<body>
  @yield('content')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
