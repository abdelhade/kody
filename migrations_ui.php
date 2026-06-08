<?php
/**
 * Premium Database Migrations Dashboard UI
 * Purpose: واجهة رسومية لإدارة وتتبع جداول قاعدة البيانات
 * Date: 2026-06-09
 */

// Safe authorization check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/classes/MigrationManager.php';

$manager = new MigrationManager($conn);

// Handle AJAX actions
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    
    if ($action === 'status') {
        try {
            $status = $manager->getMigrationStatus();
            echo json_encode(['success' => true, 'status' => $status]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'run' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = $manager->runPending();
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage(), 'logs' => ['❌ Error: ' . $e->getMessage()]]);
        }
        exit;
    }
    
    if ($action === 'rollback' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = $manager->rollback();
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage(), 'logs' => ['❌ Error: ' . $e->getMessage()]]);
        }
        exit;
    }
    
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $name = $_POST['name'] ?? '';
            $type = $_POST['type'] ?? 'php';
            if (empty($name)) {
                echo json_encode(['success' => false, 'error' => 'Migration description is required.']);
                exit;
            }
            $file = $manager->create($name, $type);
            echo json_encode(['success' => true, 'file' => $file]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'view') {
        try {
            $file = $_GET['file'] ?? '';
            $file = basename($file);
            $filePath = __DIR__ . '/database/migrations/' . $file;
            if (!file_exists($filePath)) {
                echo json_encode(['success' => false, 'error' => 'Migration file not found.']);
                exit;
            }
            $content = file_get_contents($filePath);
            echo json_encode(['success' => true, 'content' => $content, 'file' => $file]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة هجرة قاعدة البيانات - Focus Migrations</title>
    <meta name="description" content="Manage and execute database migrations easily on Focus system.">
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b0f19;
            --glass-bg: rgba(20, 26, 44, 0.65);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            
            --primary: #3b82f6;
            --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --success: #10b981;
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning: #f59e0b;
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger: #ef4444;
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --info: #6366f1;
            --info-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            
            --card-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 10% 20%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(99, 102, 241, 0.15) 0px, transparent 50%);
        }

        header {
            padding: 24px 40px;
            background: rgba(11, 15, 25, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            font-size: 28px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 8px rgba(59, 130, 246, 0.5));
        }

        .header-logo h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header-logo span {
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
            background: rgba(59, 130, 246, 0.15);
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-back {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .btn-back:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--glass-border);
            transform: translateX(-3px);
        }

        main {
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 40px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
        }

        @media (max-width: 1024px) {
            main {
                grid-template-columns: 1fr;
            }
        }

        /* Glass Panel Base */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--card-shadow);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
        .stat-icon.green { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .stat-icon.orange { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .stat-icon.purple { background: rgba(99, 102, 241, 0.15); color: #6366f1; border: 1px solid rgba(99, 102, 241, 0.3); }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-num {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Timeline / Main Panel */
        .timeline-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-box {
            position: relative;
            width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 36px 10px 16px;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .search-box i {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Timeline Visuals */
        .migrations-timeline {
            position: relative;
            padding-right: 20px;
            margin-top: 10px;
        }

        .migrations-timeline::before {
            content: '';
            position: absolute;
            top: 10px;
            right: 4px;
            bottom: 10px;
            width: 2px;
            background: var(--glass-border);
            z-index: 1;
        }

        .migration-item {
            position: relative;
            padding-right: 32px;
            margin-bottom: 24px;
            animation: fadeInUp 0.4s ease forwards;
        }

        .migration-item::before {
            content: '';
            position: absolute;
            top: 14px;
            right: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--text-muted);
            border: 3px solid var(--bg-color);
            z-index: 2;
            transition: all 0.3s ease;
        }

        .migration-item.applied::before {
            background: var(--success);
            box-shadow: 0 0 8px var(--success);
        }

        .migration-item.pending::before {
            background: var(--warning);
            box-shadow: 0 0 8px var(--warning);
        }

        .migration-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .migration-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .migration-card.pending {
            border-right: 4px solid var(--warning);
        }

        .migration-card.applied {
            border-right: 4px solid var(--success);
        }

        .migration-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .migration-name-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .migration-file {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
        }

        .badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge.bg-success {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge.bg-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge.bg-blue {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge.bg-info {
            background: rgba(99, 102, 241, 0.15);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .migration-meta {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .migration-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .migration-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-icon {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        /* Sidebar Section */
        .sidebar-panel {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .actions-panel {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
            filter: brightness(1.1);
        }

        .btn-primary-gradient:active {
            transform: translateY(0);
        }

        .btn-danger-outline {
            background: transparent;
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.4);
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-danger-outline:hover {
            background: rgba(239, 68, 68, 0.08);
            border-color: var(--danger);
            transform: translateY(-1px);
        }

        /* Form Controls */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-group input, .form-group select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
        }

        /* Console Output Card */
        .console-panel {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .console-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-clear-console {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s ease;
        }

        .btn-clear-console:hover {
            color: var(--text-main);
        }

        .console-box {
            background: #060913;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 16px;
            height: 200px;
            overflow-y: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.6;
            color: #38bdf8;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.8);
            scroll-behavior: smooth;
        }

        .console-line {
            margin-bottom: 4px;
            word-break: break-all;
            white-space: pre-wrap;
        }

        .console-line.success { color: #34d399; }
        .console-line.error { color: #f87171; }
        .console-line.info { color: #818cf8; }
        .console-line.warn { color: #fbbf24; }

        /* Modal styling */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal-content {
            background: #0f1626;
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            width: 700px;
            max-width: 90%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal.active .modal-content {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .btn-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .btn-close:hover {
            color: var(--text-main);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .code-viewer {
            background: #050811;
            border-radius: 8px;
            padding: 16px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #e2e8f0;
            line-height: 1.5;
            white-space: pre-wrap;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .empty-icon {
            font-size: 48px;
            color: rgba(255,255,255,0.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Glowing overlay */
        .glow-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            background: radial-gradient(circle at var(--x, 0px) var(--y, 0px), rgba(59, 130, 246, 0.08) 0%, transparent 60%);
            border-radius: inherit;
            z-index: 0;
        }
    </style>
</head>
<body>

    <header>
        <div class="header-logo">
            <i class="fa-solid var(--logo-icon-class, fa-database) logo-icon"></i>
            <h2>نظام هجرة قاعدة البيانات</h2>
            <span>Focus Migrations</span>
        </div>
        <div class="header-actions">
            <a href="dashboard.php" class="btn-back">
                <i class="fa-solid fa-chevron-left"></i>
                العودة للوحة التحكم
            </a>
        </div>
    </header>

    <main>
        <!-- Top Statistics Grid -->
        <section class="stats-grid" aria-label="Migration Statistics">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-num" id="stat-total">0</span>
                    <span class="stat-label">إجمالي الملفات</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-num" id="stat-applied">0</span>
                    <span class="stat-label">المنفذة بنجاح</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-circle-notch"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-num" id="stat-pending">0</span>
                    <span class="stat-label">المعلقة (قيد الانتظار)</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-num" id="stat-batch">0</span>
                    <span class="stat-label">أحدث دفعة (Batch)</span>
                </div>
            </div>
        </section>

        <!-- Right Side Panel: Controls & Create -->
        <section class="sidebar-panel">
            <!-- Execution Card -->
            <div class="glass-panel">
                <h3 class="panel-title" style="margin-bottom: 20px;">
                    <i class="fa-solid fa-sliders" style="color: var(--primary);"></i>
                    لوحة التحكم بالعمليات
                </h3>
                <div class="actions-panel">
                    <button id="btn-run-migrations" class="btn-primary-gradient">
                        <i class="fa-solid fa-play"></i>
                        تنفيذ العمليات المعلقة
                    </button>
                    <button id="btn-rollback-migrations" class="btn-danger-outline">
                        <i class="fa-solid fa-rotate-left"></i>
                        تراجع عن آخر دفعة (Rollback)
                    </button>
                </div>
            </div>

            <!-- Create Card -->
            <div class="glass-panel">
                <h3 class="panel-title" style="margin-bottom: 20px;">
                    <i class="fa-solid fa-file-circle-plus" style="color: var(--success);"></i>
                    إنشاء ملف هجرة جديد
                </h3>
                <form id="create-migration-form" onsubmit="event.preventDefault(); createMigration();">
                    <div class="form-group">
                        <label for="migration-name">اسم العملية (بالإنجليزي)</label>
                        <input type="text" id="migration-name" placeholder="مثال: add_users_status_field" required>
                    </div>
                    <div class="form-group">
                        <label for="migration-type">نوع الملف</label>
                        <select id="migration-type">
                            <option value="php">PHP Class (موصى به - يدعم التراجع الذكي)</option>
                            <option value="sql">SQL Script (استعلامات SQL مباشرة)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary-gradient" style="background: var(--success-gradient); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); width: 100%;">
                        <i class="fa-solid fa-plus"></i>
                        توليد الملف
                    </button>
                </form>
            </div>
        </section>

        <!-- Left Side Panel: Timeline List -->
        <section class="glass-panel" style="position: relative;">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="fa-solid fa-timeline" style="color: var(--info);"></i>
                    خط جدول الهجرات الزمني
                </h3>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="search-input" placeholder="بحث باسم الملف..." oninput="filterMigrations()">
                </div>
            </div>
            
            <div class="migrations-timeline" id="migrations-list">
                <!-- Timeline items loaded dynamically via JS -->
                <div class="empty-state">
                    <div class="spinner empty-icon"><i class="fa-solid fa-circle-notch"></i></div>
                    <p>جاري تحميل حالة قاعدة البيانات...</p>
                </div>
            </div>
        </section>

        <!-- Bottom Panel: Console Log Drawer -->
        <section class="glass-panel console-panel">
            <div class="console-title">
                <h3 class="panel-title">
                    <i class="fa-solid fa-terminal" style="color: var(--warning);"></i>
                    سجل مخرجات الخادم (Terminal Output)
                </h3>
                <button onclick="clearConsole()" class="btn-clear-console">
                    <i class="fa-solid fa-trash-can"></i>
                    مسح السجل
                </button>
            </div>
            <div class="console-box" id="console-output">
                <div class="console-line info">[نظام الهجرة] متصل وجاهز للعمل.</div>
            </div>
        </section>
    </main>

    <!-- Modal for Code Viewer -->
    <div class="modal" id="code-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-filename">اسم ملف الهجرة</h3>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <pre class="code-viewer"><code id="code-content"></code></pre>
            </div>
        </div>
    </div>

    <!-- JavaScript logic -->
    <script>
        let migrationsData = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadStatus();
            
            // Mouse glow effect
            document.querySelectorAll('.glass-panel').forEach(card => {
                const glow = document.createElement('div');
                glow.className = 'glow-overlay';
                card.style.position = 'relative';
                card.appendChild(glow);
                
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    glow.style.setProperty('--x', `${x}px`);
                    glow.style.setProperty('--y', `${y}px`);
                });
            });

            // Action bindings
            document.getElementById('btn-run-migrations').addEventListener('click', runMigrations);
            document.getElementById('btn-rollback-migrations').addEventListener('click', rollbackMigrations);
        });

        // Add log line to Console
        function logToConsole(text, type = 'info') {
            const consoleBox = document.getElementById('console-output');
            const line = document.createElement('div');
            line.className = `console-line ${type}`;
            
            const time = new Date().toLocaleTimeString('en-US', { hour12: false });
            line.textContent = `[${time}] ${text}`;
            
            consoleBox.appendChild(line);
            consoleBox.scrollTop = consoleBox.scrollHeight;
        }

        function clearConsole() {
            document.getElementById('console-output').innerHTML = '<div class="console-line info">[نظام الهجرة] تم تفريغ شاشة السجل.</div>';
        }

        // Fetch migrations status
        async function loadStatus(quiet = false) {
            if (!quiet) logToConsole('جاري جلب حالة قاعدة البيانات من الخادم...');
            try {
                const res = await fetch('migrations_ui.php?action=status');
                const data = await res.json();
                
                if (data.success) {
                    migrationsData = data.status;
                    renderTimeline(migrationsData);
                    updateStats(migrationsData);
                    if (!quiet) logToConsole('تم تحديث حالة الهجرات بنجاح.', 'success');
                } else {
                    logToConsole('❌ خطأ في تحميل الحالة: ' + data.error, 'error');
                }
            } catch (err) {
                logToConsole('❌ خطأ في الاتصال بالخادم: ' + err.message, 'error');
            }
        }

        // Render timeline UI
        function renderTimeline(list) {
            const container = document.getElementById('migrations-list');
            if (list.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-database empty-icon"></i>
                        <h4>لا يوجد ملفات هجرة حالياً</h4>
                        <p>ابدأ بإنشاء أول ملف هجرة باستخدام النموذج الجانبي.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            list.forEach(m => {
                const isApplied = m.applied;
                const statusBadge = isApplied 
                    ? `<span class="badge bg-success"><i class="fa-solid fa-circle-check"></i> منفذة</span>` 
                    : `<span class="badge bg-warning"><i class="fa-solid fa-circle-notch"></i> معلقة</span>`;
                
                const batchBadge = isApplied 
                    ? `<span class="badge bg-blue"><i class="fa-solid fa-layer-group"></i> الدفعة #${m.batch}</span>` 
                    : '';

                const ext = m.file.split('.').pop().toUpperCase();
                const typeBadge = `<span class="badge bg-info">${ext}</span>`;

                const timeStr = isApplied && m.applied_at ? m.applied_at : '-';

                const card = document.createElement('div');
                card.className = `migration-item ${isApplied ? 'applied' : 'pending'}`;
                card.innerHTML = `
                    <div class="migration-card ${isApplied ? 'applied' : 'pending'}">
                        <div class="migration-info">
                            <div class="migration-name-row">
                                <span class="migration-file" title="${m.file}">${m.file}</span>
                                ${statusBadge}
                                ${batchBadge}
                                ${typeBadge}
                            </div>
                            <div class="migration-meta">
                                <span><i class="fa-regular fa-clock"></i> تاريخ التنفيذ: ${timeStr}</span>
                            </div>
                        </div>
                        <div class="migration-actions">
                            <button onclick="viewFile('${m.file}')" class="btn-icon" title="عرض محتوى الملف">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Filter search list
        function filterMigrations() {
            const query = document.getElementById('search-input').value.toLowerCase();
            const filtered = migrationsData.filter(m => m.file.toLowerCase().includes(query));
            renderTimeline(filtered);
        }

        // Update stats top counters
        function updateStats(list) {
            const total = list.length;
            const applied = list.filter(m => m.applied).length;
            const pending = total - applied;
            
            // Find max batch
            let maxBatch = 0;
            list.forEach(m => {
                if (m.applied && m.batch > maxBatch) {
                    maxBatch = m.batch;
                }
            });

            document.getElementById('stat-total').textContent = total;
            document.getElementById('stat-applied').textContent = applied;
            document.getElementById('stat-pending').textContent = pending;
            document.getElementById('stat-batch').textContent = maxBatch;
        }

        // Run pending migrations
        async function runMigrations() {
            const btn = document.getElementById('btn-run-migrations');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch spinner"></i> جاري التنفيذ...';
            btn.disabled = true;
            
            logToConsole('بدء تنفيذ العمليات المعلقة...', 'info');
            
            try {
                const res = await fetch('migrations_ui.php?action=run', {
                    method: 'POST'
                });
                const data = await res.json();
                
                if (data.logs) {
                    data.logs.forEach(l => {
                        const isSuccess = l.includes('success') || l.includes('✅');
                        const isFail = l.includes('fail') || l.includes('❌');
                        const type = isSuccess ? 'success' : (isFail ? 'error' : 'info');
                        logToConsole(l, type);
                    });
                }
                
                if (data.success) {
                    if (data.count > 0) {
                        logToConsole(`🎉 نجح تنفيذ ${data.count} عملية(عمليات) جديدة بنجاح! الدفعة #${data.batch}`, 'success');
                    } else {
                        logToConsole('✨ قاعدة البيانات محدثة بالفعل ولا يوجد عمليات معلقة.', 'success');
                    }
                    loadStatus(true);
                } else {
                    logToConsole('❌ توقف التنفيذ بسبب خطأ: ' + (data.error || 'خطأ مجهول'), 'error');
                    loadStatus(true);
                }
            } catch (err) {
                logToConsole('❌ فشل إرسال الطلب للخادم: ' + err.message, 'error');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }

        // Rollback last batch
        async function rollbackMigrations() {
            if (!confirm('تحذير: هل أنت متأكد من التراجع عن الدفعة الأخيرة؟ قد يؤدي هذا إلى حذف جداول أو أعمدة وبياناتها!')) {
                return;
            }

            const btn = document.getElementById('btn-rollback-migrations');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch spinner"></i> جاري التراجع...';
            btn.disabled = true;
            
            logToConsole('بدء التراجع عن الدفعة الأخيرة...', 'warn');
            
            try {
                const res = await fetch('migrations_ui.php?action=rollback', {
                    method: 'POST'
                });
                const data = await res.json();
                
                if (data.logs) {
                    data.logs.forEach(l => {
                        const isSuccess = l.includes('completed') || l.includes('✅') || l.includes('Rolled back');
                        const isFail = l.includes('fail') || l.includes('❌');
                        const type = isSuccess ? 'success' : (isFail ? 'error' : 'warn');
                        logToConsole(l, type);
                    });
                }
                
                if (data.success) {
                    if (data.count > 0) {
                        logToConsole(`🎉 تم التراجع عن ${data.count} عملية(عمليات) بنجاح.`, 'success');
                    } else {
                        logToConsole('✨ لا يوجد عمليات مسجلة للتراجع عنها.', 'warn');
                    }
                    loadStatus(true);
                } else {
                    logToConsole('❌ فشل التراجع: ' + (data.error || 'خطأ غير معروف'), 'error');
                    loadStatus(true);
                }
            } catch (err) {
                logToConsole('❌ فشل الاتصال بالخادم أثناء التراجع: ' + err.message, 'error');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }

        // Create new migration
        async function createMigration() {
            const nameInput = document.getElementById('migration-name');
            const typeSelect = document.getElementById('migration-type');
            
            const name = nameInput.value.trim();
            const type = typeSelect.value;
            
            if (!name) return;
            
            logToConsole(`جاري إنشاء ملف الهجرة باسم ${name}...`, 'info');
            
            try {
                const formData = new FormData();
                formData.append('name', name);
                formData.append('type', type);
                
                const res = await fetch('migrations_ui.php?action=create', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (data.success) {
                    logToConsole(`🆕 تم إنشاء الملف بنجاح: database/migrations/${data.file}`, 'success');
                    nameInput.value = '';
                    loadStatus(true);
                } else {
                    logToConsole('❌ فشل إنشاء الملف: ' + data.error, 'error');
                }
            } catch (err) {
                logToConsole('❌ خطأ في إرسال طلب الإنشاء: ' + err.message, 'error');
            }
        }

        // View migration file content
        async function viewFile(filename) {
            try {
                const res = await fetch(`migrations_ui.php?action=view&file=${encodeURIComponent(filename)}`);
                const data = await res.json();
                
                if (data.success) {
                    document.getElementById('modal-filename').textContent = data.file;
                    
                    // Basic HTML escaping
                    const escapedContent = data.content
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                        
                    document.getElementById('code-content').innerHTML = escapedContent;
                    document.getElementById('code-modal').classList.add('active');
                } else {
                    alert('خطأ في عرض الملف: ' + data.error);
                }
            } catch (err) {
                alert('فشل الاتصال بالخادم لقراءة الملف.');
            }
        }

        function closeModal() {
            document.getElementById('code-modal').classList.remove('active');
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', e => {
            const modal = document.getElementById('code-modal');
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>
