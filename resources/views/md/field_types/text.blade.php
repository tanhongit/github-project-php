@if($fromValue != null && $toValue != null)
**`{{ $fieldName }}`** has been changed:
- From **`{{ $fromValue }}`**
- To **`{{ $toValue }}`**.
@elseif ($toValue == null)
**`{{ $fieldName }}`** has been removed from **`{{ $fromValue }}`**.
@else
**`{{ $fieldName }}`** has been set to **`{{ $toValue }}`**.
@endif
