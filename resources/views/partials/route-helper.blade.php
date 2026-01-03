@php
    // Detect if current route is for manager or admin by checking the URL path
    $currentPath = request()->path();
    $routePrefix = str_starts_with($currentPath, 'manager/') || str_starts_with($currentPath, 'manager') ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp
