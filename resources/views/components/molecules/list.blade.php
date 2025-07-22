@if (isset($item['children']) && is_array($item['children']))
    @php
        $isOpen = $item['active'];
    @endphp

    <li class="nav-item">
        <a class="nav-link {{ $isOpen ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
            data-target="#collapse-{{ $item['key'] }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
            aria-controls="collapse-{{ $item['key'] }}">
            <i class="fas fa-fw fa-folder {{ $item['active'] ? 'text-white' : '' }}"></i>
            <span class="font-weight-bold" style="font-size: 1rem;">{{ $item['title'] }}</span>
        </a>
        <div id="collapse-{{ $item['key'] }}" class="collapse {{ $isOpen ? 'show' : '' }}"
            aria-labelledby="heading-{{ $item['key'] }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @foreach ($item['children'] as $child)
                    <a class="collapse-item {{ $child['active'] ? 'active' : '' }}"
                        href="{{ isset($child['url']) ? route($child['url']) : '#' }}"
                        style="font-size: 0.95rem; font-weight: 500;">
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
            <span class="font-weight-bold" style="font-size: 1rem;">{{ $item['title'] }}</span>
        </a>
    </li>
@endif
