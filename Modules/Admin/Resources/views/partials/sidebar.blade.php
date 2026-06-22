<div class="main-nav">

    <div class="logo-box">
        <a href="{{ route('admin.dashboard.index') }}" class="logo-dark">
            <img src="./backoffice/assets/logo-sm.png" class="logo-sm" alt="logo sm">
            <img src="./backoffice/assets/logo-dark.png" class="logo-lg" alt="logo dark">
        </a>

        <a href="{{ route('admin.dashboard.index') }}" class="logo-light">
            <img src="./backoffice/assets/logo-sm.png" class="logo-sm" alt="logo sm">
            <img src="./backoffice/assets/logo-light.png" class="logo-lg" alt="logo light">
        </a>
    </div>

    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>
    {!! $sidebar !!}
</div>

