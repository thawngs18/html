<?php
// Tải thư viện HTML Purifier
require_once 'vendor/autoload.php';

// --- Giả lập Database ---
// Trong thực tế, đây là mảng lưu trữ các bình luận đã được làm sạch trong Database.
$database = [];

// --- Cấu hình HTML Purifier (Chỉ chạy một lần) ---
$config = HTMLPurifier_Config::createDefault();
// Bắt buộc UTF-8
$config->set('Core.Encoding', 'UTF-8');
// Tắt Cache khi test
$config->set('Cache.DefinitionImpl', null); 

// White-list: Chỉ cho phép b (in đậm), i (in nghiêng), p (đoạn văn), a[href] (liên kết)
$config->set('HTML.Allowed', 'p,b,i,a[href]'); 
// Cấm các thuộc tính nguy hiểm như style/onclick/onerror...
$config->set('CSS.AllowedProperties', []); 
// Cấm giao thức javascript:
$config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]); 

$purifier = new HTMLPurifier($config);

// --- Xử lý POST Request (Bước 1: Nhận Input) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $raw_comment = $_POST['comment'];
    
    // --- BƯỚC 2: LỌC/LÀM SẠCH (Sanitization) ---
    // Áp dụng HTML Purifier lên dữ liệu thô
    $clean_comment = $purifier->purify($raw_comment);
    
    // --- BƯỚC 3: LƯU VÀO DATABASE (Giả lập) ---
    // Chỉ lưu dữ liệu đã được làm sạch
    $database[] = [
        'timestamp' => date('H:i:s'),
        'raw' => $raw_comment,
        'clean' => $clean_comment
    ];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ứng dụng Bình Luận An Toàn</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .comment-box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .raw-input { background: #fee; border-left: 5px solid red; padding: 10px; margin-top: 10px; font-size: 0.8em; }
        .clean-output { background: #eff; border-left: 5px solid green; padding: 10px; margin-top: 10px; }
        textarea { width: 100%; height: 80px; padding: 10px; border: 1px solid #007bff; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

    <h1>Hệ thống Bình luận tích hợp HTML Purifier</h1>

    <div class="comment-box">
        <h2>Viết bình luận mới</h2>
        <form method="POST">
           
            
            <textarea name="comment" placeholder="Nhập bình luận có định dạng HTML hoặc mã độc..."></textarea><br>
            <button type="submit">Gửi Bình Luận & Lọc</button>
        </form>
    </div>

    <hr>

    <h2>Bình Luận</h2>
    
    <?php if (empty($database)): ?>
        <p>Chưa có bình luận nào. Hãy gửi bình luận đầu tiên!</p>
    <?php endif; ?>

    <?php foreach ($database as $comment): ?>
        <div class="comment-box">
            
            <p><strong>Thời gian:</strong> <?= htmlspecialchars($comment['timestamp']) ?></p>
            
            <p><strong>Dữ liệu gốc:</strong></p>
            <pre class="raw-input">
                <?= htmlspecialchars($comment['raw']) ?>
            </pre>
            
            <p><strong>Dữ liệu sau khi lọc:</strong></p>
            <pre class="clean-output" style="color: green; font-weight: bold;">
                <?= htmlspecialchars($comment['clean']) ?>
            </pre>

            <p><strong>Kết quả Hiển thị Trực tiếp:</strong></p>
            <div style="border: 1px solid #007bff; padding: 10px;">
                <?= $comment['clean'] ?> 
            </div>
            
        </div>
    <?php endforeach; ?>

</body>
</html>