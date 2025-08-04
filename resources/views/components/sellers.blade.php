<ul class="navbar-nav header-right main-notification">
    <li class="nav-item dropdown header-profile">
        <a class="nav-link" href="#" role="button" data-toggle="dropdown">
            <div class="header-info" style="display: inline">
                <span>
                    @if(session()->has('sellerId'))
                        {{ session()->get('sellerName') }}
                    @else
                        Профиль
                    @endif
                </span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            @foreach($sellers as $seller)
                <a href="/settings/changeSeller/{{ $seller->id }}" class="dropdown-item ai-icon">
                    <span class="ml-2">{{ $seller->name }}</span>
                </a>
            @endforeach
            <a href="/login/logout" class="dropdown-item ai-icon">
                <svg id="icon-logout" xmlns="https://www.w3.org/2000/svg" class="text-danger"
                     width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="ml-2">Выход </span>
            </a>
        </div>
    </li>
</ul>
