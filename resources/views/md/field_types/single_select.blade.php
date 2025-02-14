@if($fromValue != null)
**{{ $fieldName }}** has been changed from $${\color{{{ $payload['from']['color'] }}}{{ $fromValue }}$$ to $${\color{{{ $payload['to']['color'] }}}{{ $toValue }}$$.
@else
**{{ $fieldName }}** has been set to $${\color{{{ $payload['to']['color'] }}}{{ $toValue }}$$.
@endif
