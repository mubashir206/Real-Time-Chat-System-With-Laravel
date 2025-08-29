<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Live Chat System')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- pusher link  --}}
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <style>
        body {
            background: linear-gradient(145deg, #2c3e50 0%, #3498db 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #d1d9e6;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        .sidebar h4 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .sidebar .nav-link, .sidebar .dropdown-item {
            color: #2c3e50;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active,
        .sidebar .dropdown-item:hover, .sidebar .dropdown-item.active {
            background: #3498db;
            color: #ffffff;
        }
        .header {
            background: #ffffff;
            border-bottom: 1px solid #d1d9e6;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            z-index: 900;
            position: fixed;
            top: 0;
        }
        .header h3 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 2rem;
            flex-grow: 1;
            min-height: calc(100vh - 60px);
        }
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }
        .content-card:hover {
            transform: translateY(-5px);
        }
        .content-card h2 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0 0 1.5rem;
        }
        .alert {
            font-size: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            max-width: 600px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
        .table {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .table th, .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }
        .table th {
            background: #f8fafc;
            color: #2c3e50;
            font-weight: 600;
        }
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
        }
        .pagination {
            font-size: 0.9rem;
        }
        .pagination .page-link {
            border-radius: 6px;
            color: #3498db;
            transition: all 0.3s ease;
        }
        .pagination .page-link:hover {
            background: #3498db;
            color: #ffffff;
        }
        .pagination .page-item.active .page-link {
            background: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }
        @media (max-width: 576px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #d1d9e6;
                padding: 1rem;
            }
            .main-content {
                margin-left: 0;
                margin-top: 120px;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h3>@yield('header-title', 'Live Chat System')</h3>
    </div>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
