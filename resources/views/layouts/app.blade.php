<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SISFO SARPRAS</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script type="module">
      import { createIcons } from "https://cdn.jsdelivr.net/npm/lucide@latest/+esm";
      createIcons();
    </script>

    <style>
      body {
        font-family: 'Inter', sans-serif;
      }
    </style>

    @stack('head')
</head>
<body x-data="{ sidebarOpen: true }" class="bg-gray-100 text-gray-800 overflow-x-hidden">

    <div class="min-h-screen">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
