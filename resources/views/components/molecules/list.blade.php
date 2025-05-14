
<li class="nav-item">
    <a class="nav-link" href="{{ isset($list['url']) ? route($list['url']) : '#' }}">
        <p class="{{ $list['active'] ? 'btn-dashboard-dark' : 'btn-dashboard-primary' }}">
            {{ $list['title'] }}</p>
    </a>
</li>
