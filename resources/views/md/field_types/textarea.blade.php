@php
    $truncateLength = 200;
    $fromValue = $fromValue ?? '';
    $toValue = $toValue ?? '';
    
    $fromTruncated = Str::limit($fromValue, $truncateLength);
    $toTruncated = Str::limit($toValue, $truncateLength);
    
    $fromWasTruncated = $fromValue !== $fromTruncated;
    $toWasTruncated = $toValue !== $toTruncated;
@endphp

@if($fromValue && $toValue)
    **`{{ $fieldName }}`** has been updated:
    
    <details>
        <summary>View changes</summary>
        
        ### From:
        {{ $fromTruncated }}
        @if($fromWasTruncated)
            *... (content truncated)*
        @endif
        
        ### To:
        {{ $toTruncated }}
        @if($toWasTruncated)
            *... (content truncated)*
        @endif
    </details>
@elseif($toValue)
    **`{{ $fieldName }}`** has been set:
    
    <details>
        <summary>View content</summary>
        {{ $toTruncated }}
        @if($toWasTruncated)
            *... (content truncated)*
        @endif
    </details>
@else
    **`{{ $fieldName }}`** has been cleared.
@endif
