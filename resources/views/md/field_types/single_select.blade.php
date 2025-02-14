@php
    $fromColor = $fieldData['from']['color'] ?? null;
    $toColor = $fieldData['to']['color'] ?? null;
@endphp

@if($fromValue != null && $toValue != null)
    **`{{ $fieldName }}`** has been changed from $${\color{<?= $fromColor ?>}{{ $fromValue }}}$$ to $${\color{<?= $toColor ?>}{{ $toValue }}}$$.
@elseif ($toValue == null)
    The value of **`{{ $fieldName }}`** has been removed.
@else
    **`{{ $fieldName }}`** has been set to $${\color{<?= $toColor ?>}{{ $toValue }}}$$.
@endif
