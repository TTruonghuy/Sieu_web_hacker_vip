<?php
session_start();
ob_start();
require 'connectSql.php'; // Kết nối cơ sở dữ liệu
include 'header.php'; // Bao gồm header

// Lấy ID tiêu đề nếu có
$tieudeID = isset($_GET['tieudeID']) ? $_GET['tieudeID'] : null;

// Lấy tiêu đề tương ứng từ cơ sở dữ liệu
$tieude = '';
if ($tieudeID) {
    $stmt = $conn->prepare("SELECT tieude FROM tieu_de WHERE tieudeID = ?");
    $stmt->bind_param("i", $tieudeID);
    $stmt->execute();
    $stmt->bind_result($tieude);
    $stmt->fetch();
    $stmt->close();
}

// Xử lý thêm bài viết
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noidung'])) {
    $noidung = $_POST['noidung'];
    $user_id = 1; // Giả sử bạn đã xác thực và lấy ID người dùng

    // Xử lý upload ảnh
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "imagespost/"; // Thư mục để lưu ảnh
        $imageName = basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . uniqid() . "_" . $imageName; // Tạo tên file duy nhất

        // Di chuyển file đến thư mục lưu trữ
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $message = "Có lỗi xảy ra khi upload ảnh!";
        }
    }

    // Thêm bài viết mới
    $stmt = $conn->prepare("INSERT INTO baiviet (noidung, user_id, tieudeID, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $noidung, $user_id, $tieudeID, $imagePath);

    if ($stmt->execute()) {
        $message = "Bài viết đã được thêm thành công!";
        header("Location: post.php?tieudeID=" . $tieudeID); // Chuyển hướng về trang hiện tại để hiển thị bài viết
        exit();
    } else {
        $message = "Có lỗi xảy ra khi thêm bài viết!";
    }
    $stmt->close();
}

// Lấy danh sách bài viết theo tiêu đề
$posts = [];
if ($tieudeID) {
    // Cập nhật truy vấn để lấy tên người dùng và hình ảnh từ bảng user
    $stmt = $conn->prepare("SELECT b.id, b.noidung, b.created_at, b.image_path, u.fullname, u.avt 
                             FROM baiviet b 
                             JOIN users u ON b.user_id = u.id 
                             WHERE b.tieudeID = ? 
                             ORDER BY b.created_at DESC");
    $stmt->bind_param("i", $tieudeID);
    $stmt->execute();
    $stmt->bind_result($id, $noidung, $created_at, $image_path, $fullname, $avt);

    while ($stmt->fetch()) {
        $posts[] = ['id' => $id, 'noidung' => $noidung, 'created_at' => $created_at, 'image_path' => $image_path, 'fullname' => $fullname, 'avt' => $avt];
    }
    $stmt->close();
}

$conn->close();
?>

<div class="container mt-5">
    <h2>Bài Viết về Chủ Đề: <?= htmlspecialchars($tieude) ?></h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-primary" onclick="toggleForm()">Thêm Bài Viết</button>

    <div id="newPostForm" style="display: none; margin-top: 20px;">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="noidung" class="form-label">Nội Dung</label>
                <textarea class="form-control" id="noidung" name="noidung" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Chọn Ảnh</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-success">Thêm Bài Viết</button>
            <button type="button" class="btn btn-secondary" onclick="toggleForm()">Hủy</button>
        </form>
    </div>

    <h3 class="mt-4">Danh Sách Bài Viết</h3>
    <div class="list-group">
        <?php foreach ($posts as $post): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <?php if ($post['avt']): ?>
                            <img src="<?= htmlspecialchars($post['avt']) ?>" alt="Avatar" class="rounded-circle"
                                style="width: 40px; height: 40px; margin-right: 10px;">
                        <?php endif; ?>
                        <h5 class="mb-1"><?= htmlspecialchars($post['fullname']) ?> (Bài Viết ID: <?= $post['id'] ?>)</h5>
                    </div>
                    <small><?= date("d-m-Y H:i", strtotime($post['created_at'])) ?></small>
                </div>
                <p class="mb-1"><?= htmlspecialchars($post['noidung']) ?></p>
                <?php if ($post['image_path']): ?>
                    <div class="text-center mt-2">
                        <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Image"
                            style="max-width: 100%; height: auto;" class="img-fluid">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function toggleForm() {
        var form = document.getElementById("newPostForm");
        if (form.style.display === "none") {
            form.style.display = "block"; // Hiển thị form
        } else {
            form.style.display = "none"; // Ẩn form
        }
    }
</script>

<?php include 'footer.php'; // Bao gồm footer ?>