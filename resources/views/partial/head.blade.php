<meta charset="utf-8" />
<title>PengMe | @yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="@yield('metaDescription')" />
<meta name="author" content="@yield('metaAuthor')" />
<meta name="keywords" content="@yield('metaKeywords')" />

@stack('metaTag')

@php($favVer = env('FAVICON_VER', 1))
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon-512.png') }}?v={{ $favVer }}">

<!-- ================== BEGIN BASE CSS STYLE ================== -->
<link href="/assets/css/vendor.min.css" rel="stylesheet" />
<link href="/assets/css/app.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- ================== END BASE CSS STYLE ================== -->

@stack('css')
