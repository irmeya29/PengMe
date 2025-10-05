<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"{{ (!empty($htmlAttribute)) ? $htmlAttribute : '' }}>
<head>
	@include('partial.head')
</head>

<body class="{{ (!empty($bodyClass)) ? $bodyClass : '' }}">
	<!-- BEGIN #app -->
	<div id="app" class="app {{ (!empty($appClass)) ? $appClass : '' }}">
	  @includeWhen(empty($appHeaderHide), 'partial.header')
		@includeWhen(empty($appSidebarHide), 'partial.sidebar')
		@includeWhen(!empty($appTopNav), 'partial.top-nav')

    {{-- ===== Bandeau impersonation (admin connecté en tant qu’entreprise) ===== --}}
    @if(session()->has('impersonate_admin_id') && auth('web')->check())
      <div id="impersonationBar" class="impersonation-bar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2 text-white">
            <i class="bi bi-person-check title"></i>
            <span class="title">
              Mode impersonation actif — connecté comme
              <strong>{{ auth('web')->user()->name ?? 'Entreprise' }}</strong>
            </span>
          </div>
          <form method="post" action="{{ route('admin.stopImpersonate') }}">
            @csrf
            <button class="btn btn-light btn-sm">
              <i class="bi bi-box-arrow-left me-1"></i> Arrêter
            </button>
          </form>
        </div>
      </div>

      {{-- Style local (non intrusif) --}}
      <style>
        /* Couleurs projet */
        :root {--impersonation-yellow:rgba(255, 200, 3, 0.18);--impersonation-blue:#175278;}
		

        /* Bandeau collant au-dessus du contenu (ne casse pas la navbar/side) */
        #impersonationBar.impersonation-bar{
          position: sticky; top: 0; z-index: 106;
          background: var(--impersonation-yellow);
          padding: .5rem 1rem;
          box-shadow: 0 4px 10px rgba(23,82,120,.15);
		  margin-left: 230px;
		  

        }
		#impersonationBar .title { 
			font-size: .9rem;
			color: var(--impersonation-blue);
		}
		#impersonationBar .title .bi { 
			
			font-size: 1.2rem; 
			color: var(--impersonation-blue);
		
		}

        #impersonationBar .btn { border-radius: 999px; }
      </style>
    @endif
    {{-- ===== Fin bandeau impersonation ===== --}}

		@if (empty($appContentHide))
			<!-- BEGIN #content -->
			<div id="content" class="app-content  {{ (!empty($appContentClass)) ? $appContentClass : '' }}">
				@yield('content')
			</div>
			<!-- END #content -->
		@else
    	@yield('content')
		@endif

		@includeWhen(!empty($appFooter), 'partial.footer')
	</div>
	<!-- END #app -->

	@yield('outter_content')
	@include('partial.scroll-top-btn')
	@include('partial.scripts')
</body>
</html>
