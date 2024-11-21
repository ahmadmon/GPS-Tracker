@if ($paginator->hasPages())
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-primary pagin-border-primary">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0)" aria-label="Previous">
                        <span aria-hidden="true">«</span><span class="sr-only">Previous</span></a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0)"
                       dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                       wire:loading.attr="disabled" rel="prev" aria-label="@lang('pagination.previous')"
                       wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    >
                        <span aria-hidden="true">«</span><span class="sr-only">Previous</span></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span
                            class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"
                                wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"
                                aria-current="page"
                            >
                                <a class="page-link"
                                   wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"
                                   href="javascript:void(0)">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item"
                                wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}">
                                <a class="page-link" type="button"
                                   wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0)"
                       dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                       wire:click="nextPage('{{ $paginator->getPageName() }}')"
                       wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')"><span
                            aria-hidden="true">»</span><span class="sr-only">Next</span>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0)" aria-label="Next"><span
                            aria-hidden="true">»</span><span class="sr-only">Next</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif
