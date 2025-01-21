@if ($pagination['totalPages'] > 1)
<nav aria-label="Page navigation" class="mt-3">
    <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item {{ $pagination['currentPage'] == 1 ? 'disabled' : '' }}">
            <a class="page-link" href="{{ getCurrentUrl(['page'=>1])}}" aria-label="First">
                <span aria-hidden="true">&laquo; First</span>
            </a>
        </li>

        @if ($pagination['currentPage'] > 3)
        <li class="page-item"><span class="page-link">...</span></li>
        @endif
        @for ($i = max(1, $pagination['currentPage'] - 1); $i <= min($pagination['totalPages'], $pagination['currentPage'] + 1); $i++)
            <li class="page-item {{ $pagination['currentPage'] == $i ? 'active' : '' }}">
            <a class="page-link" href="{{ getCurrentUrl(['page'=> $i ])}}">{{ $i }}</a>
            </li>
            @endfor
            @if ($pagination['currentPage'] < $pagination['totalPages'] - 2)
                <li class="page-item"><span class="page-link">...</span></li>
                @endif

                <li class="page-item {{ $pagination['currentPage'] == $pagination['totalPages'] ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ getCurrentUrl(['page'=> $pagination['totalPages']]) }}" aria-label="Last">
                        <span aria-hidden="true">Last &raquo;</span>
                    </a>
                </li>
    </ul>
</nav>
@endif