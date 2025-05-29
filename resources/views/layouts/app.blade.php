<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            background: var(--bs-body-bg);
            transition: background-color 0.3s ease;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 240px;
            background: linear-gradient(135deg, var(--bs-dark) 80%, var(--bs-primary) 100%);
            color: #fff;
            padding-top: 40px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .sidebar h3 {
            font-weight: bold;
            letter-spacing: 2px;
        }
        .sidebar nav a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 24px;
            display: block;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        .sidebar nav a.active, .sidebar nav a:hover {
            background: var(--bs-primary);
            color: #fff;
            transform: translateX(5px);
        }
        .sidebar form {
            padding: 0 24px;
        }
        .main-content {
            margin-left: 240px;
            padding: 40px 32px 32px 32px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--bs-danger);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--bs-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100vw;
                height: auto;
                position: relative;
                padding-top: 20px;
            }
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
    @yield('head')
</head>
<body>
    <div class="loading-overlay">
        <div class="spinner"></div>
    </div>
    <div class="sidebar d-flex flex-column align-items-center">
        <h3 class="mb-4">School MIS</h3>
        <nav class="w-100">
            @auth
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i>Dashboard
                </a>
                @if(auth()->user()->role === 'principal')
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i>Manage Courses
                    </a>
                @elseif(auth()->user()->role === 'teacher')
                    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i>My Courses
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i>My Courses
                    </a>
                @endif
                <div class="mt-4 px-3">
                    <button id="themeToggle" class="btn btn-outline-light w-100 mb-3">
                        <i class="fas fa-moon me-2"></i>Toggle Theme
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
                    <i class="fas fa-user-plus me-2"></i>Register
                </a>
            @endauth
        </nav>
    </div>
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            const icon = themeToggle.querySelector('i');
            icon.className = newTheme === 'light' ? 'fas fa-moon me-2' : 'fas fa-sun me-2';
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', savedTheme);
        const icon = themeToggle.querySelector('i');
        icon.className = savedTheme === 'light' ? 'fas fa-moon me-2' : 'fas fa-sun me-2';

        // Loading Overlay
        function showLoading() {
            document.querySelector('.loading-overlay').style.display = 'flex';
        }

        function hideLoading() {
            document.querySelector('.loading-overlay').style.display = 'none';
        }

        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Pusher Configuration
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
        });

        const channel = pusher.subscribe('notifications');
        channel.bind('new-notification', function(data) {
            toastr.info(data.message);
        });
    </script>
    @yield('scripts')
</body>
</html> 