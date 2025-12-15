{{--@php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp

<link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
<script src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>--}}
@php
    $manifestPath = public_path('build/.vite/manifest.json');
    $manifest = json_decode(file_get_contents($manifestPath), true);

    $js = $manifest['resources/js/app.js']['file'];
    $cssFiles = $manifest['resources/js/app.js']['css'] ?? [];
@endphp

@foreach ($cssFiles as $css)
    <link rel="stylesheet" href="{{ asset('build/' . $css) }}">
@endforeach

<script src="{{ asset('build/' . $js) }}" defer></script>

