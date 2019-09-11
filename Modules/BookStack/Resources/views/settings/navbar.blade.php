
<nav class="active-link-list">
    @if($currentUser->can('settings-manage'))
        <a href="{{ url('/settings') }}" @if($selected == 'settings') class="active" @endif>@icon('settings'){{ trans('bookstack::settings.settings') }}</a>
        <a href="{{ url('/settings/maintenance') }}" @if($selected == 'maintenance') class="active" @endif>@icon('spanner'){{ trans('bookstack::settings.maint') }}</a>
    @endif
    @if($currentUser->can('users-manage'))
        <a href="{{ url('/settings/users') }}" @if($selected == 'users') class="active" @endif>@icon('users'){{ trans('bookstack::settings.users') }}</a>
    @endif
    @if($currentUser->can('user-roles-manage'))
        <a href="{{ url('/settings/roles') }}" @if($selected == 'roles') class="active" @endif>@icon('lock-open'){{ trans('bookstack::settings.roles') }}</a>
    @endif
</nav>