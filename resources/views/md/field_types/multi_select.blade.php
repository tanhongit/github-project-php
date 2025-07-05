@php
    // Convert to arrays if they aren't already
    $fromValues = is_array($fromValue) ? $fromValue : (array)($fromValue ?? []);
    $toValues = is_array($toValue) ? $toValue : (array)($toValue ?? []);
    
    // Get added and removed values
    $added = [];
    $removed = [];
    
    foreach ($toValues as $value) {
        $found = false;
        foreach ($fromValues as $fromVal) {
            if (($value['id'] ?? $value) === ($fromVal['id'] ?? $fromVal)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $added[] = $value;
        }
    }
    
    foreach ($fromValues as $value) {
        $found = false;
        foreach ($toValues as $toVal) {
            if (($value['id'] ?? $value) === ($toVal['id'] ?? $toVal)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $removed[] = $value;
        }
    }
    
    // Helper function to get display value
    $getDisplayValue = function($item) {
        if (is_array($item)) {
            $color = $item['color'] ?? null;
            $name = $item['name'] ?? $item['title'] ?? 'Unknown';
            return $color ? "`{$name}`" : $name;
        }
        return $item;
    };
@endphp

@if(count($added) > 0 || count($removed) > 0)
    **`{{ $fieldName }}`** has been updated:
    
    @if(count($added) > 0)
        - Added: {!! implode(', ', array_map($getDisplayValue, $added)) !!}
    @endif
    
    @if(count($removed) > 0)
        - Removed: {!! implode(', ', array_map($getDisplayValue, $removed)) !!}
    @endif
@elseif(empty($toValues))
    All values have been removed from **`{{ $fieldName }}`**.
@else
    **`{{ $fieldName }}`** has been set to: {!! implode(', ', array_map($getDisplayValue, $toValues)) !!}
@endif
