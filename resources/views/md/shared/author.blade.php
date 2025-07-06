@php
    $date = $date ?? now();
    $formattedDate = $date instanceof \DateTimeInterface
        ? $date->format('Y-m-d H:i:s')
        : \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
@endphp

<div style="display: flex; align-items: center; gap: 8px; margin-top: 16px; font-size: 14px; color: #6a737d;">
    @if($avatar_url ?? false)
        <img
            src="{{ $avatar_url }}"
            alt="{{ $name }}"
            width="20"
            height="20"
            style="border-radius: 50%;"
        >
    @endif
    <span>
        Changed by <a href="{{ $html_url }}" style="color: #0366d6; text-decoration: none;">{{ $name }}</a>
        @if($formattedDate)
            <span style="color: #6a737d;">on {{ $formattedDate }}</span>
        @endif
    </span>
</div>
