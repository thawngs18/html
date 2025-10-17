<?php
// Trang chọn ứng dụng chạy trong thư mục hiện tại
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ứng dụng demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Chọn Ứng Dụng Để Chạy</h1>
            <p class="mt-2 text-gray-600">Chọn một trong hai bên dưới để mở nhanh.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card: HTML Purifier Demo -->
            <a href="index.php" class="group block rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-blue-400 hover:shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 group-hover:text-blue-700">Demo HTML Purifier - Ngăn chặn XSS</h2>
                <p class="text-gray-600 mt-2 text-sm">Chương 3: KẾT QUẢ THỰC NGHIỆM. So sánh khu vực dễ tổn thương và đã được bảo vệ, kèm thời gian xử lý.</p>
                <div class="mt-4">
                    <span class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition group-hover:bg-blue-700">Mở</span>
                </div>
            </a>

            <!-- Card: Single File Comment App -->
            <a href="single_file_comment_app.php" class="group block rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-emerald-400 hover:shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 group-hover:text-emerald-700">Ứng dụng Bình luận (Single File)</h2>
                <p class="text-gray-600 mt-2 text-sm">Ứng dụng PHP một file đơn giản để đăng và hiển thị bình luận (JSON).</p>
                <div class="mt-4">
                    <span class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition group-hover:bg-emerald-700">Mở</span>
                </div>
            </a>
        </div>


        

 
    </div>
</body>
</html>


