@php
    $fromColor = $fieldData['from']['color'] ?? null;
    $toColor = $fieldData['to']['color'] ?? null;
@endphp
@if($fromValue != null)
**{{ $fieldName }}** has been changed from $${\color{{{ $fromColor }}}{{ $fromValue }}$$ to $${\color{{{ $toColor }}}{{ $toValue }}$$.
@elseif ($toValue != null)
**{{ $fieldName }}** has been removed from $${\color{{{ $fromColor }}}{{ $fromValue }}}$$.
@else
**{{ $fieldName }}** has been set to $${\color{{{ $toColor }}}{{ $toValue }}$$.
@endif
