@props(['title' => 'Gov Budget Tracker', 'page' => 'app'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Government Budget Management System</title>
    @vite(['resources/css/app.css', 'resources/js/nav.js', 'resources/js/pages/' . $page . '.js'])
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <x-nav />

    {{ $slot }}

    <x-footer />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>

</html>
