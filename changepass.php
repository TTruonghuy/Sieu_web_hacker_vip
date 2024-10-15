<?php
require 'connectSql.php'; // Kết nối đến cơ sở dữ liệu

// Khởi tạo biến để lưu thông tin phản hồi
$ketQuaLoi = "";
$ketQuaThanhCong = "";

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit;
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $username = $_SESSION['username'];
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Kiểm tra tính hợp lệ của mật khẩu mới
    if ($newPassword !== $confirmPassword) {
        $ketQuaLoi = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
    } elseif (strlen($newPassword) < 1) {
        $ketQuaLoi = "Mật khẩu không được để trống.";
    } else {
        // Kiểm tra mật khẩu cũ
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Nếu tìm thấy người dùng, kiểm tra mật khẩu cũ
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();

            // Kiểm tra mật khẩu cũ
            if ($oldPassword === $hashedPassword) { // So sánh trực tiếp mật khẩu
                // Cập nhật mật khẩu mới vào cơ sở dữ liệu
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $updateStmt->bind_param("ss", $newPassword, $username);

                if ($updateStmt->execute()) {
                    $ketQuaThanhCong = "Đổi mật khẩu thành công!";
                } else {
                    $ketQuaLoi = "Có lỗi khi cập nhật mật khẩu: " . $updateStmt->error;
                }
                $updateStmt->close();
            } else {
                $ketQuaLoi = "Mật khẩu cũ không chính xác.";
            }
        } else {
            $ketQuaLoi = "Người dùng không tồn tại.";
        }
        $stmt->close();
    }
}

mysqli_close($conn); // Đóng kết nối
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Đường dẫn tới file CSS Bootstrap -->
</head>
<body>
<div class="container">
    <h2>Đổi Mật Khẩu</h2>
    <?php if ($ketQuaLoi): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $ketQuaLoi; ?>
        </div>
    <?php elseif ($ketQuaThanhCong): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $ketQuaThanhCong; ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="old_password" class="form-label">Mật khẩu cũ</label>
            <input type="password" class="form-control" id="old_password" name="old_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary" name="change_password">Đổi Mật Khẩu</button>
    </form>
</div>

</body>
</html>
