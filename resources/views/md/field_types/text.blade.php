@if($fromValue != null)
**`{{ $fieldName }}`** has been changed from **`{{ $fromValue }}`** to **`{{ $toValue }}`**.
@elseif ($toValue != null)
**`{{ $fieldName }}`** has been removed from **`{{ $fromValue }}`**.
@else
**`{{ $fieldName }}`** has been set to **`{{ $toValue }}`**.
@endif
