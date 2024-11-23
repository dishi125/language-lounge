<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
        <a href="#">{{ env('APP_NAME') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
        <a href="#">{{ env('SHORT_APP_NAME') }}</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Request::route()->getName() == 'admin.dashboard' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.dashboard.title') }}"><i class="fas fa-fire"></i> <span>{{ __('pages.dashboard.title') }}</span></a>
            </li>
            <li class="{{ Request::route()->getName() == 'admin.users' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.users') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.user.title') }}"><i class="fas fa-users"></i> <span>{{ __('pages.user.title') }}</span></a>
            </li>
            <li class="{{ Request::route()->getName() == 'admin.banner' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.banner') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.banner.title') }}"><i class="fas fa-users"></i> <span>{{ __('pages.banner.title') }}</span></a>
            </li>
            <li class="{{ Request::route()->getName() == 'admin.important-settings' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.important-settings') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.important_settings.title') }}"><i class="fas fa-cog"></i> <span>{{ __('pages.important_settings.title') }}</span></a>
            </li>
            <li class="{{ Request::route()->getName() == 'coupon' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.coupon.index') }}"  data-toggle="tooltip" data-original-title="coupon"><i class="fas fa-list"></i> <span>Coupon</span></a>
            </li>
        </ul>
    </aside>
</div>
