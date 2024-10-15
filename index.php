<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Null</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <?php
    session_start();
    ob_start();
    include 'header.php';
    include 'slider.php';
    echo '<div class="container px-4 py-5 my-5">';

    // Get the current page
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    ?>
    <div class="row g-5">
        <div class="col-md-8 d-flex align-items-center flex-column">
            <?php
            switch ($page) {
                case 'signup':
                    include "./signup.php";
                    break;
                case "login":
                    include './login.php';
                    break;
                case "changepass";
                    include './changepass.php';
                    break;
                case "logout":
                    session_unset();
                    session_destroy();
                    header("Location: index.php?page=login");
                    break;
                case "bannerup":
                    include './bannerup.php';
                    break;
                case "edittopic":
                    include './edittopic.php';
                    break;
                default:
                    include './home.php';
                    break;
            }
            ?>
        </div>
    </div>
    <?php
    echo '</div>';
    include 'topic.php';
    ?>
    <?php
    echo '</div>';
    include 'footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-6p3LkeGhrGFc5pZqTox0c71uV9Pk8sFcGfdAYAWwG4FhEVGVK1CTPS0ANXsU+8G6"
        crossorigin="anonymous"></script>

</body>

</html>