<div class="sidebar">
    <h4>Live Chat</h4>
    <nav class="nav flex-column">
        <a class="nav-link @if(Route::currentRouteName() == 'users.index') active @endif" href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a>
        <a class="nav-link @if(Route::currentRouteName() == 'personal.chat') active @endif" href="{{ route('personal.chat') }}"><i class="fas fa-comment"></i> Personal Chat</a>
        <a class="nav-link @if(Route::currentRouteName() == 'group.chat') active @endif" href="{{ route('group.chat') }}"><i class="fas fa-comments"></i> Group Chat</a>
        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle @if(in_array(Route::currentRouteName(), ['settings', 'logout'])) active @endif" href="#" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog"></i> Settings
            </a>
            <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                <li><a class="dropdown-item @if(Route::currentRouteName() == 'settings') active @endif" href="{{ route('settings') }}">Personal Settings</a></li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item @if(Route::currentRouteName() == 'logout') active @endif" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Auth User Info -->
    <div class="auth-user mt-auto p-3 border-top">
        <div class="d-flex align-items-center">
            <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" 
                 style="width: 40px; height: 40px; background: #3498db; color: #ffffff; font-size: 0.9rem;">
                {{ Auth::user()->name[0] ?? 'U' }}
            </div>
            <div>
                <strong>{{ Auth::user()->name ?? 'User' }}</strong>
                <small class="d-block text-muted">Online</small>
            </div>
        </div>
    </div>
</div>