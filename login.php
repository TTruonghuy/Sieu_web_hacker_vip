<?php
require 'connectSql.php'; // Kết nối tới cơ sở dữ liệu

// Kiểm tra trạng thái session trước khi khởi động
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session chỉ nếu chưa có session nào
}
$username = $password = ""; // Khởi tạo biến

// Hàm xử lý dữ liệu đầu vào
function test_input($data)
{
    $data = trim($data); // Xóa khoảng trắng đầu và cuối
    $data = stripslashes($data); // Xóa dấu /
    $data = htmlspecialchars($data); // Chuyển các ký tự đặc biệt sang mã HTML
    return $data;
}

// Kiểm tra nếu người dùng đã nhấn nút "Submit"
if (isset($_POST['submitLogin'])) {
    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);

    // Sử dụng prepared statement để bảo vệ khỏi SQL Injection
   // $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
 //   $stmt->bind_param("s", $username); // Liên kết biến với câu lệnh
   // $stmt->execute();
  //  $result = $stmt->get_result();s

     $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
     $result = $conn->query($sql);
    // Kiểm tra xem tài khoản có tồn tại
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            // Chuyển hướng đến trang tải lên
            header("Location: index.php?page=EditLiveshow");
            exit();
        } else {
            echo '<div class="alert alert-danger" role="alert">';
            echo "Tài khoản hoặc mật khẩu không đúng";
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">';
        echo "Tài khoản hoặc mật khẩu không đúng"; // Thông báo chung cho cả tài khoản và mật khẩu
        echo '</div>';
    }

    //$stmt->close(); // Đóng prepared statement
mysqli_close($conn); // Đóng kết nối cơ sở dữ liệu
?>

<!-- Form đăng nhập -->
<div style="max-width: 600px; width: 600px">
    <h2>Đăng nhập</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập username" required>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput2" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>
        <div>
            <input type="submit" class="btn btn-primary" style="width: 100%;" name="submitLogin" value="Đăng nhập">
        </div>
    </form>
</div>
