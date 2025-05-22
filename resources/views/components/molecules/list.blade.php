@if (isset($item['children']) && is_array($item['children']))
    @php
        $isOpen = $item['active']; // menu parent aktif jika salah satu child aktif
    @endphp

    <li class="nav-item">
        <a class="nav-link {{ $isOpen ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
            data-target="#collapse-{{ $item['key'] }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
            aria-controls="collapse-{{ $item['key'] }}">
            <i class="fas fa-fw fa-folder {{ $item['active'] ? 'text-white' : '' }}"></i>
            <span>{{ $item['title'] }}</span>
        </a>
        <div id="collapse-{{ $item['key'] }}" class="collapse {{ $isOpen ? 'show' : '' }}"
            aria-labelledby="heading-{{ $item['key'] }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @foreach ($item['children'] as $child)
                    <a class="collapse-item {{ $child['active'] ? 'active' : '' }}"
                        href="{{ isset($child['url']) ? route($child['url']) : '#' }}">
                        {{ $child['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </li>
@else
    <li class="nav-item">
        <a class="nav-link {{ $item['active'] ? 'active' : '' }}"
            href="{{ isset($item['url']) ? route($item['url']) : '#' }}">
            <i class="fas fa-fw fa-circle {{ $item['active'] ? 'text-white' : '' }}"></i>
            <span>{{ $item['title'] }}</span>
        </a>
    </li>
@endif
