<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WELLMEADOWS - Patient Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        /* Top Bar */
        .top-bar { background: #83D475; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo-section { display: flex; align-items: baseline; gap: 10px; }
        .logo-section h1 { color: white; font-size: 1.6rem; font-weight: 700; margin: 0; }
        .logo-section span { color: white; font-size: 0.8rem; opacity: 0.9; }
        .user-section { display: flex; align-items: center; gap: 20px; }
        .user-section span, .user-section i { color: white; }
        .logout-btn { background: none; border: none; color: white; cursor: pointer; }

        /* Navigation Tabs */
        .nav-tabs-custom { background: white; border-bottom: 1px solid #e5e7eb; padding: 0 30px; display: flex; gap: 0; }
        .nav-tab { padding: 14px 24px; font-size: 0.9rem; font-weight: 500; color: #6b7280; text-decoration: none; border-bottom: 3px solid transparent; }
        .nav-tab:hover { color: #83D475; }
        .nav-tab.active { color: #83D475; border-bottom-color: #83D475; }

        /* Page Header */
        .page-header { background: white; padding: 20px 30px; border-bottom: 1px solid #e5e7eb; }
        .page-header h2 { font-size: 1.5rem; font-weight: 600; color: #1f2937; margin: 0; }
        .page-header p { color: #6b7280; font-size: 0.85rem; margin: 5px 0 0; }

        /* Main Content */
        .main-content { padding: 24px 30px; max-width: 1400px; }

        /* Stat Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-bottom: 3px solid; }
        .stat-card.total { border-bottom-color: #83D475; }
        .stat-card.admissions { border-bottom-color: #3b82f6; }
        .stat-card.beds { border-bottom-color: #eab308; }
        .stat-card.records { border-bottom-color: #8b5cf6; }
        .stat-value { font-size: 2rem; font-weight: 700; color: #1f2937; }
        .stat-label { font-size: 0.7rem; color: #6b7280; text-transform: uppercase; margin-top: 5px; }

        /* Action Buttons */
        .action-buttons { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .btn-primary-custom { background: #83D475; color: white; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; }
        .btn-primary-custom:hover { background: #6bc85c; }
        .btn-secondary-custom { background: #e5e7eb; color: #374151; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; }

        /* Cards */
        .form-card, .table-card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { background: #f9fafb; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; font-weight: 600; }
        .card-body { padding: 20px; }

        /* Form Inputs */
        .form-input { width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px; }
        .form-input:focus { outline: none; border-color: #83D475; }
        .form-label { font-size: 0.7rem; font-weight: 600; color: #4b5563; margin-bottom: 4px; display: block; }

        /* Grid */
        .form-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .full-width { grid-column: span 4; }

        /* Table */
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table th { text-align: left; padding: 12px; background: #f9fafb; font-size: 0.75rem; font-weight: 600; }
        .custom-table td { padding: 12px; font-size: 0.8rem; border-bottom: 1px solid #f0f0f0; }
        .custom-table tr:hover { background: #f9fafb; }
        .table-action-btn { background: #e5e7eb; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; margin: 0 2px; cursor: pointer; }
        .table-action-btn.danger { background: #fee2e2; color: #dc2626; }
        .record-card { background: #f9fafb; border-radius: 8px; padding: 12px; margin-bottom: 12px; border: 1px solid #e5e7eb; }

        .mt-4 { margin-top: 16px; }
        .mb-4 { margin-bottom: 16px; }
        .w-100 { width: 100%; }
        .text-center { text-align: center; }
        .py-8 { padding: 32px 0; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .form-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
            .full-width { grid-column: span 2; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="logo-section">
            <h1>WELLMEADOWS</h1>
            <span>HOSPITAL</span>
        </div>
        <div class="user-section">
            <i class="fas fa-user-circle"></i>
            <span>admin ▼</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="nav-tabs-custom">
        <a href="{{ route('dashboard') }}" class="nav-tab">Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-tab active">Patient Management</a>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h2>Patient Management</h2>
        <p>Register and manage patient records</p>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>