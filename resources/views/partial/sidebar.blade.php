<!-- BEGIN #sidebar -->
<div id="sidebar" class="app-sidebar">
  <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
    <div class="menu">
      @php
        // Sélection du menu selon le guard
        $menuItems = auth('admin')->check()
          ? config('sidebar_admin.menu')
          : config('sidebar_company.menu');
      @endphp

      @foreach ($menuItems as $menu)
        @if (!empty($menu['is_header']))
          <div class="menu-header">{{ $menu['text'] }}</div>

        @elseif (!empty($menu['is_divider']))
          <div class="menu-divider"></div>

        @else
          @php
            // Active par préfixe (ex: /employees et /employees/123)
            $url = $menu['url'] ?? '#';
            $pattern = ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/'); // sécurise
            $isActive = $url !== '#' && request()->is($pattern) || request()->is($pattern.'/*');
          @endphp
          <div class="menu-item {{ $isActive ? 'active' : '' }}">
            <a href="{{ $url }}" class="menu-link">
              @if(!empty($menu['icon']))
                <span class="menu-icon"><i class="{{ $menu['icon'] }}"></i></span>
              @endif
              <span class="menu-text">{{ $menu['text'] ?? '' }}</span>
            </a>
          </div>
        @endif
      @endforeach

      <div class="p-3 px-4 mt-auto hide-on-minified">
        <a href="#" class="btn btn-secondary d-block w-100 fw-600 rounded-pill">
          <i class="fa fa-code-branch me-1 ms-n1 opacity-75"></i> NOVASOL
        </a>
      </div>
    </div>
  </div>
  <button class="app-sidebar-mobile-backdrop" data-dismiss="sidebar-mobile"></button>
</div>
<!-- END #sidebar -->

{{-- Styles DIRECTS (pas besoin de @push) --}}
<style>
  :root { --primary:#175278; --accent:#FECA0A; --hover:#4A90B6; }

  /* Fond + texte */
  #sidebar.app-sidebar { background: var(--primary) !important; color:#fff !important; }
  #sidebar .menu-header { text-transform: uppercase; font-size:.75rem; font-weight:600;
    padding:.75rem 1rem .25rem; color: rgba(255,255,255,.7) !important; }
  #sidebar .menu-divider { height:1px; background: rgba(255,255,255,.2); margin:.5rem 0; }

  /* Items */
  #sidebar .menu-item { margin:.2rem 0; }
  #sidebar .menu-link {
    display:flex; align-items:center; gap:.75rem;
    padding:.6rem 1rem; border-radius:.5rem;
    color:#fff !important; text-decoration:none;
    transition: background .15s ease, color .15s ease;
  }
  /* Hover */
  #sidebar .menu-link:hover { background: var(--hover) !important; color:#fff !important; }

  /* Actif */
  #sidebar .menu-item.active > .menu-link {
    background: var(--accent) !important;
    color:#000 !important; font-weight:600;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,.05);
  }

  /* Icônes & texte */
  #sidebar .menu-icon { width:1.25rem; text-align:center; opacity:.95; }
  #sidebar .menu-text { flex:1; }

  /* Bouton bas */
  #sidebar .btn.btn-secondary {
    background: var(--accent) !important; border:none !important; color:#111 !important; font-weight:700;
  }
  #sidebar .btn.btn-secondary:hover { background: var(--hover) !important; color:#fff !important; }
</style>
