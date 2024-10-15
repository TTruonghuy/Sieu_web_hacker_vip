<?php
require 'connectSql.php'; // Kết nối tới cơ sở dữ liệu
$username = $password = $gender = $email = $fullname = "";
$ketQuaLoi = ""; // Biến lưu thông báo lỗi
$ketQuaThanhCong = ""; // Biến lưu thông báo thành công

// Hàm xử lý dữ liệu đầu vào
function test_input($data) {
    $data = trim($data); // Xóa khoảng trắng đầu và cuối
    $data = stripslashes($data); // Xóa dấu /
    $data = htmlspecialchars($data); // Chuyển các ký tự đặc biệt sang mã HTML
    return $data;
}

// Kiểm tra nếu người dùng đã nhấn nút "Submit"
if (isset($_POST['sbSubmit'])) {
    // Lấy và làm sạch dữ liệu từ biểu mẫu
    $username = test_input($_POST['username']);
    $fullname = test_input($_POST['fullname']);
    $password = test_input($_POST['password']);
    $email = test_input($_POST['email']);
    $gender = test_input($_POST['gender']);

    // Kiểm tra xem username hoặc email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // Kiểm tra định dạng email và xem liệu người dùng đã tồn tại chưa
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $result->num_rows === 0) {
        $targetDir = "images/"; // Thư mục lưu trữ ảnh
        $fileName = time() . '_' . $_FILES["fileToUpload"]["name"]; // Tạo tên file duy nhất
        $targetFilePath = $targetDir . $fileName; // Đường dẫn file
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); // Lấy kiểu file

        // Kiểm tra xem người dùng có chọn file không
        if (!empty($_FILES["fileToUpload"]["name"])) {
            // Cho phép các định dạng file cụ thể
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
            if (!in_array($fileType, $allowTypes)) {
                $ketQuaLoi = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; // Thông báo lỗi định dạng file
            } else {
                // Tải file lên server
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
                    // Chèn thông tin người dùng vào bảng users
                    $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, avt, gender, email) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $username, $password, $fullname, $targetFilePath, $gender, $email);
                    // Kiểm tra việc thực hiện câu lệnh SQL
                    if ($stmt->execute()) {
                        $ketQuaThanhCong = "Created user successfully"; // Thông báo thành công
                    } else {
                        echo "Error: " . $stmt->error; // Thông báo lỗi câu lệnh SQL
                    }
                    $stmt->close();
                } else {
                    $ketQuaLoi = 'Sorry, there was an error uploading your file.'; // Thông báo lỗi tải file
                }
            }
        } else {
            $ketQuaLoi  = 'Please select a file to upload'; // Thông báo nếu không chọn file
        }
    } else {
        $ketQuaLoi = "Error: Username or email already exists"; // Thông báo nếu người dùng đã tồn tại
    }
}

mysqli_close($conn); // Đóng kết nối cơ sở dữ liệu
?>

<!-- Form đăng ký -->
<div style="max-width: 600px; width: 600px">
    <h2>Đăng ký</h2>
    <form method="POST" action="" enctype="multipart/form-data">

        <!-- Hiển thị thông báo lỗi hoặc thành công -->
        <?php
        if ($ketQuaLoi) {
            echo '<div class="alert alert-danger" role="alert">';
            echo $ketQuaLoi;
            echo '</div>';
        } else if ($ketQuaThanhCong) {
            echo '<div class="alert alert-success" role="alert">';
            echo $ketQuaThanhCong;
            echo '</div>';
        } else {
            echo '';
        }
        ?>

        <!-- Nhập thông tin người dùng -->
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Nhập username" required>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label">Fullname</label>
            <input type="text" name="fullname" class="form-control" placeholder="Nhập fullname" required>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput2" class="form-label">Email</label>
            <input type="text" name="email" class="form-control" placeholder="Nhập email" required>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput2" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Nhập password" required>
        </div>

        <!-- Tải lên ảnh đại diện -->
        <div class="mb-3">
            <label for="formFile" class="form-label">Avatar</label>
            <input class="form-control" id="fileToUpload" name="fileToUpload" type="file" required>
        </div>
        
        <!-- Chọn giới tính -->
        <div class="mb-3">
            <label for="formFile" class="form-label">Giới tính</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="gender" type="radio" value="1" id="inlineRadio1" required>
                    <label class="form-check-label" for="inlineRadio1">Nam</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="gender" type="radio" value="0" id="inlineRadio2" required>
                    <label class="form-check-label" for="inlineRadio2">Nữ</label>
                </div>
            </div>
        </div>

        <!-- Nút submit -->
        <div>
            <input type="submit" class="btn btn-primary" style="width: 100%;" name="sbSubmit" value="Submit">
        </div>
    </form>
</div>
