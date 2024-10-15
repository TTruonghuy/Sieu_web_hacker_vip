<?php
require 'connectSql.php'; // Kết nối cơ sở dữ liệu

// Xóa tiêu đề
if (isset($_POST['delete'])) {
    $tieudeID = $_POST['tieudeID'];
    $stmt = $conn->prepare("DELETE FROM tieu_de WHERE tieudeID = ?");
    $stmt->bind_param("i", $tieudeID);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Tiêu đề đã được xóa thành công!"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Có lỗi xảy ra khi xóa tiêu đề!"]);
    }
    $stmt->close();
    exit(); // Dừng thực thi mã sau khi thực hiện xóa
}

// Thêm tiêu đề mới
if (isset($_POST['add'])) {
    $tieude = $_POST['tieude'];
    $trangthai = isset($_POST['trangthai']) ? 1 : 0; // Giá trị 1 là hiển thị, 0 là ẩn

    $stmt = $conn->prepare("INSERT INTO tieu_de (tieude, trangthai) VALUES (?, ?)");
    $stmt->bind_param("si", $tieude, $trangthai);

    if ($stmt->execute()) {
        echo "<script>alert('Tiêu đề đã được thêm thành công!');</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi thêm tiêu đề!');</script>";
    }
    $stmt->close();
}

// Lấy danh sách tiêu đề
$result = $conn->query("SELECT * FROM tieu_de");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tiêu đề</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Danh sách Tiêu đề</h2>

    <div id="message" class="alert" style="display:none;"></div>

    <!-- Form thêm tiêu đề mới -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="tieude" class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" id="tieude" name="tieude" required>
        </div>
        <div class="mb-3">
            <input type="checkbox" id="trangthai" name="trangthai">
            <label for="trangthai">Hiển thị</label>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Thêm Tiêu đề</button>
    </form>

    <!-- Hiển thị danh sách tiêu đề -->
    <table class="table table-striped mt-4">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['tieudeID'] ?>">
                <td><?= $row['tieudeID'] ?></td>
                <td><?= htmlspecialchars($row['tieude']) ?></td>
                <td><?= $row['trangthai'] ? 'Hiển thị' : 'Ẩn' ?></td>
                <td>
                    <button class="btn btn-danger btn-sm delete-btn">Xóa</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('.delete-btn').click(function() {
            var row = $(this).closest('tr');
            var tieudeID = row.data('id');

            if (confirm('Bạn có chắc chắn muốn xóa?')) {
                $.ajax({
                    type: 'POST',
                    url: '', // URL của tệp PHP hiện tại
                    data: { delete: true, tieudeID: tieudeID },
                    success: function(response) {
                        var res = JSON.parse(response);
                        $('#message').removeClass('alert-danger alert-info').addClass(res.success ? 'alert-info' : 'alert-danger');
                        $('#message').text(res.message).show();
                        if (res.success) {
                            row.fadeOut(); // Ẩn hàng đã xóa
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>

<?php $conn->close(); ?>
