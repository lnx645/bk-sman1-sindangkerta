@php
$label = str_replace('_', ' ', $status);
@endphp
<span class="badge badge-status badge-status-{{ $status }} rounded-pill"><i class="bi bi-circle-fill" style="font-size:.45rem;vertical-align:middle;"></i> {{ ucwords($label) }}</span>
