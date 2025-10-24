<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Rental Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

    <!-- Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-md z-50">
            <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-5 right-5 bg-red-600 text-white px-4 py-2 rounded shadow-md z-50">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @yield('content')
</body>
</html>
