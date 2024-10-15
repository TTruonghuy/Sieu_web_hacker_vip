<?php
require 'connectSql.php'; // Kết nối đến cơ sở dữ liệu

// Khởi tạo biến để lưu thông tin phản hồi
$ketQuaLoi = "";
$ketQuaThanhCong = "";

// Kiểm tra nếu có form được gửi đi cho upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    // Lấy dữ liệu từ form upload
    $title = $_POST['title'];
    $description = $_POST['description'];
    $username = $_SESSION['username']; // Lấy tên người dùng từ session

    // Đường dẫn để lưu ảnh
    $targetDir = "banner/"; // Thư mục để lưu ảnh
    $fileName = time() . "_" . basename($_FILES["image"]["name"]); // Tạo tên file 
    $targetFilePath = $targetDir . $fileName; // Đường dẫn hoàn chỉnh đến file

    // Kiểm tra loại file
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif'); // Các loại file cho phép

    if (in_array($fileType, $allowTypes)) {
        // Di chuyển file vào thư mục uploads
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Chuẩn bị câu lệnh SQL để chèn dữ liệu vào bảng banner
            $stmt = $conn->prepare("INSERT INTO banner (username, title, description, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $title, $description, $targetFilePath); // Gán tham số

            // Thực thi câu lệnh và kiểm tra kết quả
            if ($stmt->execute()) {
                $ketQuaThanhCong = "Upload banner thành công!"; // Thông báo thành công
            } else {
                $ketQuaLoi = "Có lỗi khi lưu vào cơ sở dữ liệu: " . $stmt->error; // Thông báo lỗi
            }
            $stmt->close(); // Đóng statement
        } else {
            $ketQuaLoi = "Có lỗi khi tải ảnh lên."; // Thông báo lỗi tải ảnh
        }
    } else {
        $ketQuaLoi = "Chỉ cho phép các định dạng JPG, JPEG, PNG, GIF."; // Thông báo lỗi định dạng
    }
}

// Xử lý xóa banner
if (isset($_GET['deleteId'])) {
    $deleteId = $_GET['deleteId'];
    // Kiểm tra ID hợp lệ
    if (!empty($deleteId) && is_numeric($deleteId)) {
        // Chuẩn bị câu lệnh SQL xóa
        $stmt = $conn->prepare("DELETE FROM banner WHERE id_images = ?");
        $stmt->bind_param("i", $deleteId);

        // Thực thi câu lệnh và kiểm tra kết quả
        if ($stmt->execute()) {
            $ketQuaThanhCong = "Xóa banner thành công!";
        } else {
            $ketQuaLoi = "Có lỗi khi xóa banner: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $ketQuaLoi = "ID không hợp lệ."; // Thông báo lỗi ID
    }
}

// Lấy danh sách banner
$result = $conn->query("SELECT * FROM banner ORDER BY id_images DESC");

// Đóng kết nối
mysqli_close($conn); 
?>

<div class="container w-100">
    <div class="row">
        <!-- Upload Form -->
        <div class="col-md-6">
            <form action="" method="POST" enctype="multipart/form-data"> <!-- Form gửi dữ liệu -->
                <div class="mb-3">
                    <div>
                        <img style="display: none; width: 200px; height: 200px; object-fit: contain" id="imgPreview" class="img-preview" src="" alt="Chưa chọn ảnh">
                    </div>
                </div>
                <div class="mb-3">
                    <h2 class="mb-5">Upload Ảnh</h2>
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Nhập tiêu đề" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Nhập mô tả" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Chọn ảnh</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage()" required>
                </div>
                <?php if ($ketQuaLoi): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $ketQuaLoi; ?>
                    </div>
                <?php elseif ($ketQuaThanhCong): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $ketQuaThanhCong; ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary" name="upload">Upload</button>
            </form>
        </div>

        <!-- Danh Sách Banner -->
        <div class="col-md-6">
            <h2 class="mt-5">Danh Sách Banner</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Mô tả</th>
                        <th>Ảnh</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_images']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Banner" style="width: 100px;"></td>
                            <td>
                                <a href="?delete_id=<?php echo $row['id_images']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function previewImage() {
        var file = document.getElementById("image").files[0]; // Lấy file từ input
        var reader = new FileReader(); // Tạo đối tượng FileReader

        reader.onload = function (e) {
            document.getElementById("imgPreview").src = e.target.result; // Hiển thị ảnh xem trước
            document.getElementById("imgPreview").style.display = "block"; // Hiển thị ảnh
        };

        reader.readAsDataURL(file); // Đọc file như một URL
    }
</script>
