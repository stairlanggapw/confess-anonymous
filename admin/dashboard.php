<?php
require_once '../config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
    $confession_id = intval($_POST['confession_id'] ?? 0);
    
    if ($confession_id > 0) {
        $stmt = $conn->prepare("UPDATE confessions SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $confession_id);
        
        if ($stmt->execute()) {
            $success = '✅ Confess berhasil disetujui!';
        } else {
            $error = 'Gagal mengsetujui confess!';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject') {
    $confession_id = intval($_POST['confession_id'] ?? 0);
    
    if ($confession_id > 0) {
        $stmt = $conn->prepare("UPDATE confessions SET status = 'rejected', approved_by = ? WHERE id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $confession_id);
        
        if ($stmt->execute()) {
            $success = '❌ Confess berhasil ditolak!';
        } else {
            $error = 'Gagal menolak confess!';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'block') {
    $user_id = intval($_POST['user_id'] ?? 0);
    
    if ($user_id > 0) {
        $stmt = $conn->prepare("UPDATE users SET is_blocked = TRUE WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success = '🚫 User berhasil diblokir!';
        } else {
            $error = 'Gagal memblokir user!';
        }
    }
}

$pending = $conn->query("SELECT COUNT(*) as count FROM confessions WHERE status = 'pending'")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) as count FROM confessions WHERE status = 'approved'")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) as count FROM confessions WHERE status = 'rejected'")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];

$stmt = $conn->prepare("
    SELECT c.id, c.message, c.created_at, u.username, u.id as user_id, u.is_blocked
    FROM confessions c
    JOIN users u ON c.user_id = u.id
    WHERE c.status = 'pending'
    ORDER BY c.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$pending_confessions = [];
while ($row = $result->fetch_assoc()) {
    $pending_confessions[] = $row;
}

$stmt = $conn->prepare("
    SELECT c.id, c.message, c.created_at, c.approved_at, u.username, u.id as user_id
    FROM confessions c
    JOIN users u ON c.user_id = u.id
    WHERE c.status = 'approved'
    ORDER BY c.approved_at DESC
    LIMIT 20
");
$stmt->execute();
$result = $stmt->get_result();
$approved_confessions = [];
while ($row = $result->fetch_assoc()) {
    $approved_confessions[] = $row;
}

$stmt = $conn->prepare("
    SELECT c.id, c.message, c.created_at, u.username, u.id as user_id
    FROM confessions c
    JOIN users u ON c.user_id = u.id
    WHERE c.status = 'rejected'
    ORDER BY c.created_at DESC
    LIMIT 20
");
$stmt->execute();
$result = $stmt->get_result();
$rejected_confessions = [];
while ($row = $result->fetch_assoc()) {
    $rejected_confessions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Confess Anonymous</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar h2 {
            color: white;
            font-size: 24px;
        }
        
        .navbar a, .navbar button {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .navbar a:hover, .navbar button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card h3 {
            color: #999;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-card.pending .number {
            color: #f39c12;
        }
        
        .stat-card.approved .number {
            color: #27ae60;
        }
        
        .stat-card.rejected .number {
            color: #e74c3c;
        }
        
        .stat-card.users .number {
            color: #3498db;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab-btn {
            padding: 12px 20px;
            background: none;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            color: #999;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }
        
        .tab-btn.active {
            color: #1e3c72;
            border-bottom-color: #1e3c72;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .confession-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            animation: slideUp 0.5s ease;
        }
        
        .confession-item.rejected {
            border-left-color: #e74c3c;
        }
        
        .confession-item.approved {
            border-left-color: #27ae60;
        }
        
        .confession-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .confession-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .confession-user-info {
            display: flex;
            flex-direction: column;
        }
        
        .confession-user-info .username {
            font-weight: 600;
            color: #333;
        }
        
        .confession-user-info .date {
            font-size: 12px;
            color: #999;
        }
        
        .confession-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-blocked {
            background: #f8d7da;
            color: #721c24;
        }
        
        .confession-content {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            word-wrap: break-word;
        }
        
        .confession-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .confession-actions form {
            display: inline;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-approve {
            background: #27ae60;
            color: white;
        }
        
        .btn-approve:hover {
            background: #229954;
            transform: translateY(-2px);
        }
        
        .btn-reject {
            background: #e74c3c;
            color: white;
        }
        
        .btn-reject:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-block {
            background: #e67e22;
            color: white;
        }
        
        .btn-block:hover {
            background: #d35400;
            transform: translateY(-2px);
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
            background: white;
            border-radius: 8px;
        }
        
        .empty-message h3 {
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🔑 Admin Dashboard</h2>
        <form method="POST" action="logout.php" style="display: inline;">
            <button type="submit">🚪 Logout</button>
        </form>
    </div>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card pending">
                <h3>📋 Menunggu Review</h3>
                <div class="number"><?= $pending ?></div>
            </div>
            <div class="stat-card approved">
                <h3>✅ Sudah Disetujui</h3>
                <div class="number"><?= $approved ?></div>
            </div>
            <div class="stat-card rejected">
                <h3>❌ Ditolak</h3>
                <div class="number"><?= $rejected ?></div>
            </div>
            <div class="stat-card users">
                <h3>👥 Total Users</h3>
                <div class="number"><?= $total_users ?></div>
            </div>
        </div>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('pending')">📋 Pending (<?= $pending ?>)</button>
            <button class="tab-btn" onclick="switchTab('approved')">✅ Approved (<?= $approved ?>)</button>
            <button class="tab-btn" onclick="switchTab('rejected')">❌ Rejected (<?= $rejected ?>)</button>
        </div>
        
        <div id="pending" class="tab-content active">
            <?php if (count($pending_confessions) > 0): ?>
                <?php foreach ($pending_confessions as $confession): ?>
                    <div class="confession-item">
                        <div class="confession-header">
                            <div class="confession-user">
                                <div>👤</div>
                                <div class="confession-user-info">
                                    <span class="username">
                                        <?= htmlspecialchars($confession['username']) ?>
                                        <?php if ($confession['is_blocked']): ?>
                                            <span class="confession-badge badge-blocked">🚫 BLOCKED</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="date">📅 <?= date('d M Y H:i', strtotime($confession['created_at'])) ?></span>
                                </div>
                            </div>
                            <span class="confession-badge badge-pending">PENDING</span>
                        </div>
                        
                        <div class="confession-content">
                            <?= htmlspecialchars($confession['message']) ?>
                        </div>
                        
                        <div class="confession-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="confession_id" value="<?= $confession['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-approve">✅ Setujui</button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="confession_id" value="<?= $confession['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-reject">❌ Tolak</button>
                            </form>
                            
                            <?php if (!$confession['is_blocked']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $confession['user_id'] ?>">
                                    <input type="hidden" name="action" value="block">
                                    <button type="submit" class="btn btn-block" onclick="return confirm('Yakin ingin memblokir user ini?');">🚫 Blokir User</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message">
                    <h3>✨ Tidak ada confess yang perlu direview</h3>
                    <p>Semua confess telah diproses!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="approved" class="tab-content">
            <?php if (count($approved_confessions) > 0): ?>
                <?php foreach ($approved_confessions as $confession): ?>
                    <div class="confession-item approved">
                        <div class="confession-header">
                            <div class="confession-user">
                                <div>👤</div>
                                <div class="confession-user-info">
                                    <span class="username"><?= htmlspecialchars($confession['username']) ?></span>
                                    <span class="date">📅 <?= date('d M Y H:i', strtotime($confession['created_at'])) ?></span>
                                </div>
                            </div>
                            <span class="confession-badge badge-approved">APPROVED</span>
                        </div>
                        
                        <div class="confession-content">
                            <?= htmlspecialchars($confession['message']) ?>
                        </div>
                        
                        <small style="color: #999;">
                            Disetujui: <?= date('d M Y H:i', strtotime($confession['approved_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message">
                    <h3>📭 Belum ada confess yang disetujui</h3>
                    <p>Mulai review confess dari tab Pending</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="rejected" class="tab-content">
            <?php if (count($rejected_confessions) > 0): ?>
                <?php foreach ($rejected_confessions as $confession): ?>
                    <div class="confession-item rejected">
                        <div class="confession-header">
                            <div class="confession-user">
                                <div>👤</div>
                                <div class="confession-user-info">
                                    <span class="username"><?= htmlspecialchars($confession['username']) ?></span>
                                    <span class="date">📅 <?= date('d M Y H:i', strtotime($confession['created_at'])) ?></span>
                                </div>
                            </div>
                            <span class="confession-badge badge-rejected">REJECTED</span>
                        </div>
                        
                        <div class="confession-content">
                            <?= htmlspecialchars($confession['message']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message">
                    <h3>✨ Belum ada confess yang ditolak</h3>
                    <p>Bagus! Tidak ada confess yang perlu ditolak.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');

            event.target.classList.add('active');
        }
    </script>
</body>
</html>