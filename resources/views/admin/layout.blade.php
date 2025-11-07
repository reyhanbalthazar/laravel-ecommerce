<!-- resources/views/admin/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <!-- Admin Navigation -->
    <nav class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Admin Panel</a>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                        <a href="{{ route('admin.products.index') }}" class="hover:text-gray-300">Products</a>
                        <a href="{{ route('admin.categories.index') }}" class="hover:text-gray-300">Categories</a>
                        <a href="{{ route('admin.orders.index') }}" class="hover:text-gray-300">Orders</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    <a href="{{ route('home') }}" class="hover:text-gray-300" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Store
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-gray-300">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>

</html>