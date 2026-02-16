<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'confess_anonymous');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function filterProfanity($text) {
    $profanity = array(
        'anjing', 'asu', 'babi', 'bangsat', 'buta', 'tonggos',
        'coli', 'cule', 'dajjal', 'dodol', 'gajah', 'goblok',
        'jancok', 'jembut', 'kampret', 'bangke', 'bangkek',
        'keparat', 'kolop', 'kompiang', 'kontol', 'keparat',
        'lonte', 'monyet', 'monyong', 'moro', 'moropohu',
        'murahan', 'murtah', 'nista', 'nistho',
        'pantat', 'peler', 'perek', 'perkosa', 'ireng',
        'pornografi', 'psikopat', 'sempak',
        'sethubil', 'setubil', 'sialan', 'sial', 'somplak', 'spesialis',
        'sundal', 'sundut', 'telek', 'temali', 'temenung',
        'temerlang', 'temberang', 'temberau', 'temboel', 'tembung', 
        'tempik', 'tempel', 'tempion', 'tempolak', 'temporil',
        'tempe', 'tempen', 'tempikang', 'tempel', 'tempolak', 'tembang',
        'tempol', 'tempoq', 'tempoq', 'tempos', 'tempoq', 'tempoy', 'tempur',
        'tempus', 'tempus', 'temwa', 'tenai', 
        'tenak', 'tenaksama', 'tenaksamaing', 'tenal', 'tenale',
        'tenalu', 'tenam', 'tenama', 'tenamba', 'tenambi', 'tenambih'
    );
    
    $text = strtolower($text);
    foreach ($profanity as $word) {
        $text = str_ireplace($word, str_repeat('*', strlen($word)), $text);
    }
    return $text;
}

?>