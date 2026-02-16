<?php
require_once 'config.php';

$stmt = $conn->prepare("
    SELECT id, message, created_at 
    FROM confessions 
    WHERE status = 'approved' 
    ORDER BY created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$confessions = [];
while ($row = $result->fetch_assoc()) {
    $confessions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confess Anonymous - Ceritakan Rahasiamu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .navbar h2 {
            color: #667eea;
            font-size: 24px;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .nav-links a, .nav-links button {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nav-links a.btn-primary {
            background: #667eea;
            color: white;
        }
        
        .nav-links a.btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        
        .nav-links button {
            background: #e74c3c;
            color: white;
        }
        
        .nav-links button:hover {
            background: #c0392b;
        }
        
        .nav-links .user-info {
            color: #333;
            font-size: 14px;
            padding: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            animation: fadeIn 0.6s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .header h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .confessions-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .confession-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.5s ease;
            transition: all 0.3s;
            border-left: 4px solid #667eea;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .confession-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .confession-content {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .confession-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }
        
        .confession-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            color: #999;
        }
        
        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .cta-section {
            text-align: center;
            margin-top: 40px;
            animation: fadeIn 0.7s ease 0.3s both;
        }
        
        .cta-section a {
            background: white;
            color: #667eea;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .cta-section a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .confession-count {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>💬 Confess Anonymous</h2>
        <div class="nav-links">
            <?php if (isLoggedIn()): ?>
                <span class="user-info">👤 <?= $_SESSION['username'] ?></span>
                <a href="confess.php" class="btn-primary">📝 Kirim Confess</a>
                <form method="POST" action="logout.php" style="display: inline;">
                    <button type="submit">🚪 Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn-primary">🔓 Login</a>
                <a href="register.php" class="btn-primary">📝 Daftar</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="header">
            <h1>🗣️ Ceritakan Rahasiamu</h1>
            <p>Berbagi cerita anonim dengan aman dan nyaman</p>
        </div>
        
        <?php if (count($confessions) > 0): ?>
            <div class="confession-count">
                📊 Total <?= count($confessions) ?> confess dari pengguna
            </div>
            
            <div class="confessions-list">
                <?php foreach ($confessions as $i => $confession): ?>
                    <div class="confession-card" style="animation-delay: <?= ($i * 0.1) ?>s;">
                        <div class="confession-icon">💭</div>
                        <div class="confession-content">
                            <?= htmlspecialchars($confession['message']) ?>
                        </div>
                        <div class="confession-meta">
                            <span>📅 <?= date('d M Y H:i', strtotime($confession['created_at'])) ?></span>
                            <span>✅ Terverifikasi</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>🤔 Belum ada confess</h3>
                <p>Jadilah yang pertama berbagi ceritamu!</p>
            </div>
        <?php endif; ?>
        
        <?php if (!isLoggedIn()): ?>
            <div class="cta-section">
                <a href="register.php">Mulai berbagi cerita anonim sekarang →</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>