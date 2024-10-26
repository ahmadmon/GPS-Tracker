<div class="select-box w-100" data-component="{{ $name }}">
    <div class="options-container">
        @forelse($options as $key => $option)
            <div class="selection-option">
                <input class="radio" id="{{ $key }}" value="{{ $key }}" type="radio" @checked(old($name, $value ?? '') == $key))>
                <label class="mb-0" for="{{ $key }}">{{ $option }}</label>
            </div>
        @empty
            <div>
               موردی یافت نشد...
            </div>
        @endforelse

    </div>
    <div class="selected-box rounded" style="padding: 9px 24px">{{ $options[old($name, $value ?? '')] ?? 'انتخاب کنید...' }}</div>
    <div class="search-box">
        <input type="text" placeholder="جستجو کردن..." aria-label="search">
    </div>
    <input type="hidden" value="{{ old($name, $value ?? '') }}" name="{{ $name }}" id="finalValue">
</div>

@pushonce('scripts')
    <script src="{{ asset('assets/js/custom/searchable-select-option.js') }}"></script>
@endpushonce
