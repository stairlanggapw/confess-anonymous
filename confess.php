<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $conn->prepare("SELECT is_blocked FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['is_blocked']) {
    $error = 'Akun Anda telah diblokir! Tidak bisa mengirim confess.';
}

$success = '';
$error = isset($error) ? $error : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $message = sanitize($_POST['message'] ?? '');
    
    // Validasi
    if (empty($message)) {
        $error = 'Pesan tidak boleh kosong!';
    } elseif (strlen($message) < 10) {
        $error = 'Pesan minimal 10 karakter!';
    } elseif (strlen($message) > 5000) {
        $error = 'Pesan maksimal 5000 karakter!';
    } else {
        // Filter kata kasar
        $filtered_message = filterProfanity($message);
        
        // Insert ke database dengan status pending
        $stmt = $conn->prepare("INSERT INTO confessions (user_id, message, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("is", $_SESSION['user_id'], $filtered_message);
        
        if ($stmt->execute()) {
            $success = '✅ Confess Anda berhasil dikirim! Menunggu persetujuan dari moderator.';
            // Clear form
            $_POST['message'] = '';
        } else {
            $error = 'Gagal mengirim confess. Coba lagi!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Confess - Confess Anonymous</title>
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
        }
        
        .nav-links button {
            background: #e74c3c;
            color: white;
        }
        
        .nav-links button:hover {
            background: #c0392b;
        }
        
        .container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
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
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .form-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.6s ease;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            resize: vertical;
            min-height: 200px;
        }
        
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .char-count {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #999;
            margin-top: 8px;
        }
        
        .char-count.warning {
            color: #e74c3c;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #060;
            border: 1px solid #cfc;
        }
        
        .alert-warning {
            background: #ffeaa7;
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        .info-box {
            background: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            color: #333;
            margin-top: 20px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.3s;
        }
        
        .back-link a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>💬 Confess Anonymous</h2>
        <div class="nav-links">
            <a href="home.php" class="btn-primary">🏠 Beranda</a>
            <form method="POST" action="logout.php" style="display: inline;">
                <button type="submit">🚪 Logout</button>
            </form>
        </div>
    </div>
    
    <div class="container">
        <div class="header">
            <h1>📝 Ceritakan Rahasiamu</h1>
            <p>Tulis pesan anonim Anda dengan aman</p>
        </div>
        
        <div class="form-card">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($user['is_blocked']) && $user['is_blocked']): ?>
                <div class="alert alert-warning">
                    🚫 Akun Anda telah diblokir oleh admin. Hubungi admin untuk informasi lebih lanjut.
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="message">💭 Pesan Anda</label>
                        <textarea id="message" name="message" placeholder="Tulis cerita atau rahasia Anda di sini... (minimal 10 karakter)" required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                        <div class="char-count">
                            <span>Karakter: <span id="char-count">0</span>/5000</span>
                            <span id="warning-text" style="display: none;">⚠️ Mendekati batas maksimal</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn" id="submit-btn">📤 Kirim Confess</button>
                </form>
                
                <div class="info-box">
                    <strong>ℹ️ Catatan Penting:</strong>
                    <ul style="margin-left: 20px; margin-top: 8px;">
                        <li>Pesan Anda akan tersimpan dengan status <strong>pending</strong></li>
                        <li>Admin akan memeriksa dan menyetujui pesan Anda</li>
                        <li>Kata kasar akan otomatis disensor</li>
                        <li>Username Anda <strong>tidak akan terlihat</strong> di halaman publik</li>
                        <li>Pesan yang setuju akan ditampilkan dengan tanggal pengiriman</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="back-link">
            <a href="home.php">← Kembali ke beranda</a>
        </div>
    </div>
    
    <script>
        const textarea = document.getElementById('message');
        const charCount = document.getElementById('char-count');
        const charCountContainer = document.querySelector('.char-count');
        const warningText = document.getElementById('warning-text');
        const submitBtn = document.getElementById('submit-btn');
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count < 10) {
                submitBtn.disabled = true;
            } else {
                submitBtn.disabled = false;
            }
            
            if (count > 4500) {
                charCountContainer.classList.add('warning');
                warningText.style.display = 'inline';
            } else {
                charCountContainer.classList.remove('warning');
                warningText.style.display = 'none';
            }
        });
        
        textarea.dispatchEvent(new Event('input'));
    </script>
</body>
</html>