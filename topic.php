<div class="col-md-4">
    <div class="position-sticky" style="top: 2rem;">
        <div class="p-4 mb-3 bg-body-tertiary rounded">
            <h4 class="fst-italic">Diễn đàn</h4>
            <p class="mb-0">Chia sẻ các khó khăn trong lập trình</p>
        </div>
        <div>
            <h4 class="fst-italic p-4 mb-3 bg-body-tertiary rounded">Chủ đề bài viết</h4>

            <!-- Bắt đầu danh sách các tiêu đề được truy vấn từ cơ sở dữ liệu -->
            <ul class="list-unstyled p-4 mb-3 bg-body-tertiary rounded">
                <?php
                // Kết nối tới cơ sở dữ liệu
                require 'connectSql.php';

                // Truy vấn danh sách các tiêu đề
                $stmt = $conn->prepare("SELECT tieudeID, tieude FROM tieu_de WHERE trangthai = 1");
                $stmt->execute();
                $stmt->bind_result($tieudeID, $tieude);

                // Hiển thị từng tiêu đề dưới dạng danh sách
                while ($stmt->fetch()) {
                    echo '
                    <li>
                        <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top"
                            href="post.php?tieudeID=' . $tieudeID . '">
                            <div class="col-lg-8">
                                <h6 class="mb-0">' . htmlspecialchars($tieude) . '</h6>
                            </div>
                        </a>
                    </li>
                    ';
                }
                // Đóng kết nối
                $stmt->close();
                $conn->close();
                ?>
            </ul>
        </div>
    </div>
</div>
