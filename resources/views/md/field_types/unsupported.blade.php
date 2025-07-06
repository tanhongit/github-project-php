@php
    // Try to format the values for display
    $formatValue = function($value) {
        if ($value === null) {
            return '`null`';
        }
        if (is_bool($value)) {
            return $value ? '`true`' : '`false`';
        }
        if (is_array($value) || is_object($value)) {
            return '```json
'.json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'
```';
        }
        return '`'.(string)$value.'`';
    };
    
    $fromFormatted = $fromValue !== null ? $formatValue($fromValue) : null;
    $toFormatted = $toValue !== null ? $formatValue($toValue) : null;
@endphp

**`{{ $fieldName }}`** ({{ $fieldType }}) has been updated:

@if($fromFormatted !== null && $toFormatted !== null)
    - From: {!! $fromFormatted !!}
    - To: {!! $toFormatted !!}
@elseif($toFormatted !== null)
    - Set to: {!! $toFormatted !!}
@else
    - Value cleared
@endif

*Note: This field type is not fully supported. Displaying raw data.*
