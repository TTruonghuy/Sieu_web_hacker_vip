<div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel" data-bs-interval="1000">
    <div class="carousel-indicators">
        <?php
        require 'connectSql.php'; // Kết nối cơ sở dữ liệu

        // Lấy danh sách banner từ cơ sở dữ liệu
        $query = "SELECT * FROM banner ORDER BY id_images DESC"; // Lấy banner từ cơ sở dữ liệu
        $result = $conn->query($query); // Thực thi truy vấn

        // Kiểm tra nếu truy vấn thành công và có kết quả
        if ($result && $result->num_rows > 0) {
            $totalImages = 0;

            // Tạo các button điều khiển cho từng slide
            while ($row = $result->fetch_assoc()) {
                $activeClass = ($totalImages === 0) ? 'active' : ''; // Đặt class 'active' cho slide đầu tiên
                echo "<button type='button' data-bs-target='#myCarousel' data-bs-slide-to='$totalImages' class='$activeClass' aria-label='Slide " . ($totalImages + 1) . "'></button>";
                $totalImages++;
            }
        } else {
            echo "<p>Không có banner nào được tìm thấy.</p>"; // Hiển thị thông báo nếu không có dữ liệu
        }
        ?>
    </div>
    <div class="carousel-inner" style="height: 500px">
        <?php
        // Đặt lại biến đếm
        if ($result && $result->num_rows > 0) {
            $totalImages = 0;
            
            // Đặt lại con trỏ kết quả và tạo các slide cho từng hình ảnh
            $result->data_seek(0); // Đặt lại con trỏ để bắt đầu từ đầu
            while ($row = $result->fetch_assoc()) {
                $activeClass = ($totalImages === 0) ? 'active' : ''; // Chỉ slide đầu tiên có class 'active'
                $imagePath = $row['image_path']; // Lấy đường dẫn ảnh từ cơ sở dữ liệu
                $title = htmlspecialchars($row['title']); // Lấy tiêu đề
                $description = htmlspecialchars($row['description']); // Lấy mô tả

                echo "<div class='carousel-item $activeClass' style='height: 500px'>";
                echo "<img class='d-block w-100' src='$imagePath' style='height: 500px; object-fit: cover;'>"; // Hiển thị ảnh
                echo "<div class='container'>";
                echo "<div class='carousel-caption text-start'>";
                echo "<h5>$title</h5>";  // Tiêu đề banner
                echo "<p>$description</p>"; // Mô tả cho ảnh
                echo "</div></div></div>";

                $totalImages++;
            }
        }
        ?>
    </div>

    <!-- Nút điều hướng của carousel -->
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
