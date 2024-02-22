@props([
    'status' => 'success',
])
<span class="badge badge-{{ $status }}">{{ $slot }}</span>