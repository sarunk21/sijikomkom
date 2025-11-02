@if (isset($item['children']) && is_array($item['children']))
    @php
        $isOpen = $item['active'];
    @endphp

    <li class="nav-item">
        <a class="nav-link {{ $isOpen ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
            data-target="#collapse-{{ $item['key'] }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
            aria-controls="collapse-{{ $item['key'] }}" style="padding: 0.75rem 1rem;">
            <i class="fas fa-fw fa-folder {{ $item['active'] ? 'text-white' : '' }}" style="font-size: 0.9rem;"></i>
            <span class="font-weight-bold" style="font-size: 0.95rem;">{{ $item['title'] }}</span>
        </a>
        <div id="collapse-{{ $item['key'] }}" class="collapse {{ $isOpen ? 'show' : '' }}"
            aria-labelledby="heading-{{ $item['key'] }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded" style="margin: 0 0.5rem;">
                @foreach ($item['children'] as $child)
                    <a class="collapse-item {{ $child['active'] ? 'active' : '' }}"
                        href="{{ isset($child['url']) ? route($child['url']) : '#' }}"
                        style="font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.25rem; transition: all 0.2s ease;">
                        <i class="fas fa-angle-right me-2" style="font-size: 0.75rem; opacity: 0.7;"></i>
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
