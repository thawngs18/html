<?php
/**
 * DEMO THỰC NGHIỆM HTML PURIFIER CHỐNG TẤN CÔNG XSS
 * Phiên bản: ĐÃ LOẠI BỎ TOÀN BỘ Tailwind CSS, chỉ dùng CSS thuần.
 */

// BƯỚC 1: Khởi tạo biến (Giữ nguyên)
$rawContent = '';
$purifiedContent = '';
$isSubmitted = false;
$errorMessage = '';
$processingTime = 0;
$diff_analysis = []; 

// Hàm phân tích sự khác biệt (Giữ nguyên)
function analyzePurification($raw, $clean) {
    $analysis = [];
    
    if ($raw === $clean && $raw !== '') {
        $analysis[] = "Nội dung **hoàn toàn hợp lệ** theo cấu hình White-list và đã được giữ nguyên. (Ví dụ: thẻ p, b, span).";
        return $analysis;
    }

    // --- Phân tích các yếu tố bị lọc ---
    if (preg_match('/<script[^>]*>|<\/script>/i', $raw) && !preg_match('/<script[^>]*>|<\/script>/i', $clean)) {
        $analysis[] = "Yếu tố bị lọc: **Thẻ &lt;script&gt;**. Lý do: Thẻ này cho phép thực thi mã JavaScript độc hại trên trình duyệt người dùng.";
    }
    if (preg_match('/on\w+=/i', $raw) && !preg_match('/on\w+=/i', $clean)) {
        $analysis[] = "Yếu tố bị lọc: **Thuộc tính sự kiện (Ví dụ: onerror)**. Lý do: Các thuộc tính 'on*' cho phép thực thi mã JavaScript khi sự kiện xảy ra.";
    }
    if (preg_match('/javascript:/i', $raw) && !preg_match('/javascript:/i', $clean)) {
        $analysis[] = "Yếu tố bị lọc: **Giao thức javascript:/**. Lý do: Được dùng để chèn mã JavaScript vào thuộc tính href (liên kết).";
    }
    if (preg_match('/expression\(|moz-binding/i', $raw) && !preg_match('/expression\(|moz-binding/i', $clean)) {
        $analysis[] = "Yếu tố bị lọc: **Hàm CSS expression()**. Lý do: Đây là một kỹ thuật tấn công CSS để thực thi mã trong trình duyệt cũ.";
    }
    if (empty($analysis) && $raw !== $clean) {
        $analysis[] = "Yếu tố bị lọc: **Các thuộc tính/thẻ không được khai báo**. Lý do: HTML Purifier sử dụng mô hình **White-list**, chỉ giữ lại những gì được cho phép và loại bỏ tất cả các yếu tố không an toàn khác (bao gồm cả các biến thể né tránh).";
    }

    return $analysis;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $isSubmitted = true;
    $rawContent = $_POST['content'];
    
    $timeStart = microtime(true);
    try {
        require_once 'vendor/autoload.php';

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null); 
        $config->set('Core.Encoding', 'UTF-8'); 
        $config->set('HTML.Allowed', 'p,a[href|title],span,b,i,u,img[src|alt]');
        $config->set('CSS.AllowedProperties', ['color', 'text-align', 'font-size']);
        $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true)); 
        $config->set('HTML.TargetBlank', true); 

        $purifier = new HTMLPurifier($config);
        $purifiedContent = $purifier->purify($rawContent);
        $diff_analysis = analyzePurification($rawContent, $purifiedContent);

    } catch (Exception $e) {
        $errorMessage = "Lỗi: Không thể tải thư viện HTML Purifier. Vui lòng kiểm tra lệnh 'composer require ezyang/htmlpurifier' đã chạy chưa.";
    }
    
    $timeEnd = microtime(true);
    $processingTime = number_format(($timeEnd - $timeStart) * 1000, 4); 
}

$xssPayloads = [
    "<script>alert('1. XSS Script');</script>", 
    "Hello <img src=x onerror=alert('2. XSS Attribute')> World", 
    "<a href='javascript:alert(3)'>3. XSS Protocol</a>", 
    "<ScRipt>alert('4. Case Evasion')</scRiPt>", 
    "<a href='&#x6A;avas&#99;ript:alert(5)'>5. Hex Encoding</a>", 
    "<p style='color: blue; text-align: center;'>6. Nội dung An toàn.</p>"
];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Purifier: Chống Tấn công XSS</title>
    <style>
        /* CSS THUẦN (THAY THẾ TAILWIND) */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f7f7f7; 
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 4px solid #3b82f6;
        }
        .card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .payload-list {
            background-color: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .payload-item {
            display: block;
            font-family: monospace;
            background-color: #fef3c7;
            padding: 5px;
            margin-top: 5px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: nowrap;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
        }
        button {
            width: 100%;
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            padding: 12px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        
        /* PHÂN TÍCH VÀ HIỆU NĂNG */
        .analysis-block {
            background-color: #e0e7ff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 5px solid #4f46e5;
            margin-bottom: 30px;
        }
        .analysis-block h2 {
            font-size: 1.25rem;
            color: #4338ca;
            margin-bottom: 15px;
        }
        .analysis-time {
            border-top: 1px solid #c7d2fe;
            padding-top: 10px;
            margin-top: 15px;
        }

        /* SO SÁNH CỘT */
        .grid {
            display: flex;
            gap: 20px;
        }
        .col {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .col h2 {
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid;
        }

        /* Màu sắc CỘT 1: VULNERABLE */
        .vulnerable {
            background-color: #fee2e2;
            border: 4px solid #ef4444;
        }
        .vulnerable h2 {
            color: #b91c1c;
            border-bottom-color: #fca5a5;
        }
        .vulnerable .raw-input-box {
            background-color: #fecaca;
        }

        /* Màu sắc CỘT 2: PROTECTED */
        .protected {
            background-color: #d1fae5;
            border: 4px solid #10b981;
        }
        .protected h2 {
            color: #065f46;
            border-bottom-color: #a7f3d0;
        }
        .protected .clean-output-box {
            background-color: #a7f3d0;
        }
        
        /* KHỐI NỘI DUNG */
        .content-box { 
            min-height: 100px; 
            background-color: white; 
            border: 1px solid #d1d5db; 
            padding: 1rem; 
            border-radius: 0.5rem; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            word-wrap: break-word;
        }
        .code-block {
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            overflow-x: auto;
            white-space: pre-wrap;
            font-size: 12px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="header">
        Thực nghiệm Chống XSS bằng HTML Purifier
    </h1>

    <div class="card">
        <h2>1. Form Thử Nghiệm</h2>
        
        <div class="payload-list">
            <p style="font-weight: bold; margin-bottom: 5px;">Các Kịch bản Tấn công XSS Tiêu biểu & Biến thể:</p>
            <?php foreach ($xssPayloads as $i => $payload): ?>
                <span class="payload-item"><?= htmlspecialchars($payload) ?></span>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" action="" style="margin-top: 20px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="content">Nội dung Thử nghiệm (HTML/Mã độc):</label>
                <textarea id="content" name="content" rows="8" placeholder="Nhập các payload XSS ở trên hoặc nội dung hợp lệ..."><?= htmlspecialchars($rawContent) ?></textarea>
            </div>
            <button type="submit">
                THỰC HIỆN LỌC VÀ PHÂN TÍCH
            </button>
        </form>
    </div>

    <?php if ($isSubmitted): ?>
        
        <?php if ($errorMessage): ?>
            <div style="background-color: #fef2f2; border: 1px solid #f87171; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 30px; font-weight: bold;">
                LỖI THỰC NGHIỆM: <span style="display: block;"><?= $errorMessage ?></span>
            </div>
        <?php endif; ?>

        <div class="analysis-block">
            <h2>4. Phân tích Yếu tố Độc hại Đã Lọc</h2>
            <ul style="list-style-type: disc; margin-left: 20px; line-height: 1.5;">
                <?php if (empty($diff_analysis)): ?>
                    <li>Không có yếu tố độc hại nào được phát hiện trong nội dung đầu vào.</li>
                <?php else: ?>
                    <?php foreach ($diff_analysis as $item): ?>
                        <li><?= $item ?></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <p class="analysis-time" style="font-weight: bold; color: #4f46e5;">Thời gian lọc: <span style="font-size: 1.25rem; margin-left: 10px; color: #312e81;"><?= $processingTime ?> ms</span></p>
        </div>

        <div class="grid">
            
            <div class="col vulnerable">
                <h2>2. ỨNG DỤNG CHƯA ĐƯỢC LỌC (VULNERABLE)</h2>
                <p style="margin-bottom: 15px; font-size: 14px; color: #991b1b; font-weight: bold;">Dữ liệu thô được xuất thẳng ra màn hình, cho phép mã độc XSS thực thi.</p>

                <h3 style="font-weight: bold; margin-bottom: 5px; color: #b91c1c;">Mã HTML Đầu vào (Raw Input):</h3>
                <pre class="code-block raw-input-box"><?= htmlspecialchars($rawContent) ?></pre>
                
                <h3 style="font-weight: bold; margin-top: 15px; margin-bottom: 5px; color: #b91c1c;">Nội dung Trực tiếp (Kết quả Thực thi):</h3>
                <div class="content-box">
                    <?= $rawContent ?>
                </div>
            </div>

            <div class="col protected">
                <h2>3. ỨNG DỤNG ĐÃ ĐƯỢC LỌC (PROTECTED)</h2>
                <p style="margin-bottom: 15px; font-size: 14px; color: #065f46; font-weight: bold;">HTML Purifier sử dụng mô hình **White-list** loại bỏ mọi thẻ và thuộc tính không an toàn.</p>

                <h3 style="font-weight: bold; margin-bottom: 5px; color: #065f46;">Mã HTML Sau khi Lọc (Clean Output):</h3>
                <pre class="code-block clean-output-box"><?= htmlspecialchars($purifiedContent) ?></pre>
                
                <h3 style="font-weight: bold; margin-top: 15px; margin-bottom: 5px; color: #065f46;">Nội dung Trực tiếp (Kết quả An toàn):</h3>
                <div class="content-box">
                    <?= $purifiedContent ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>