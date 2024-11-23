@if (Request::route()->getName() == 'admin.users')
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('pages.dashboard.title') }}</a></div>
        <div class="breadcrumb-item">{{ __('pages.user.title') }}</div>
    </div>
@endif
