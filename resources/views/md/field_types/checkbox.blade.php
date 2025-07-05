@if(isset($fromValue) && $fromValue !== $toValue)
    **`{{ $fieldName }}`** has been {{ $toValue ? 'checked' : 'unchecked' }}.
@elseif ($toValue === true)
    **`{{ $fieldName }}`** has been checked.
@elseif ($toValue === false)
    **`{{ $fieldName }}`** has been unchecked.
@else
    **`{{ $fieldName }}`** has been set to {{ $toValue ? '✅' : '❌' }}.
@endif
