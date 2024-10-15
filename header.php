<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BaiTapVip</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    .pixel-font {
        font-family: 'Press Start 2P', cursive; /* Sử dụng font pixel */
        letter-spacing: 1px; /* Khoảng cách giữa các ký tự để trông giống pixel */
    }
</style>
</head>

<body>

    <nav class="navbar navbar-expand-sm bg-white navbar-white text-black border-bottom border-dark ">
        <div class="container">
            <a class="navbar-brand " href="index.php">BAITAP</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse d-flex justify-content-between" id="collapsibleNavbar">
                <div>
                    <ul class="navbar-nav">
                        <?php
                        if (isset($_SESSION['username']) && $_SESSION['username'] != '' && isset($_SESSION['role']) && $_SESSION['role'] == 1) {
                            echo '
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=bannerup">Chỉnh sửa Banner</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link" href="index.php?page=edittopic">Chỉnh sửa chủ đề</a>
                            </li>
                        ';
                        }
                        ?>
                    </ul>
                </div>
                <div>
                    <?php
                    if (isset($_SESSION['username']) && $_SESSION['username'] != '') {
                        echo '<ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Welcome ' . htmlspecialchars($_SESSION['username']) . '</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=changepass">Đổi mật khẩu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=logout">Đăng xuất</a>
                        </li>
                        </ul>';
                    } else {
                        echo '
                          <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=login">Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=signup">Đăng ký</a>
                            </li>
                        </ul>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-6p3LkeGhrGFc5pZqTox0c71uV9Pk8sFcGfdAYAWwG4FhEVGVK1CTPS0ANXsU+8G6" crossorigin="anonymous"></script>
</body>
</html>
