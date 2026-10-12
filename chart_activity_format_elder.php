<?php
session_name("C_Smart");
session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php
    include "./modules/head.inc.php";
    include "./includes/connect.php";
    ?>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart-container {
            width: 100%;
            max-width: 700px;
            margin: auto;
        }

        .card-custom {
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            background: #fff;
        }

        body {
            background-color: #f4f6f9;
        }

        h1 {
            font-weight: bold;
        }
    </style>
</head>

<body>

<?php include "./modules/header.inc.php"; ?>

<div class="wrapper">

    <!-- Navbar -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">

                <a class="navbar-brand" href="index.php">
                    การศึกษาต่อเนื่อง
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavDropdown">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">

                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-bars"></i>
                            </a>
                        </li>
                    </ul>

                    <?php include "./modules/topblock.inc.php"; ?>

                </div>
            </div>
        </nav>
    </header>

    <!-- Sidebar -->
    <aside class="main-sidebar bg-light">
        <?php include "./sidebar.php"; ?>
    </aside>

    <!-- Content -->
    <div class="content-wrapper">

        <section class="content-header p-4">
            <h1>กราฟแสดงข้อมูลรูปแบบโครงการ</h1>

            <ol class="breadcrumb">
                <li>
                    <a href="index.php">
                        <i class="fa fa-dashboard"></i> Home
                    </a>
                </li>
                <li class="active">กราฟแสดงข้อมูลรูปแบบโครงการ</li>
            </ol>
        </section>

        <section class="content p-4">

            <div class="row">
                <div class="col-12">

                    <div class="card-custom">

                        <?php

                        $sql = "SELECT 
                                    format.format_name,
                                    COUNT(activity.format_id) AS number
                                FROM activity
                                INNER JOIN format
                                    ON activity.format_id = format.format_id
                                GROUP BY format.format_id
                                ORDER BY format.format_id";

                        try {

                            $stmt = $pdo->query($sql);

                            $labels = [];
                            $data = [];
                            $total = 0;

                            while($row = $stmt->fetch()){

                                $labels[] = $row['format_name'];
                                $data[] = $row['number'];

                                $total += $row['number'];
                            }

                        ?>

                        <div class="chart-container">
                            <canvas id="myChart"></canvas>
                        </div>

                        <div class="text-center mt-4">
                            <h4 class="text-primary">
                                โครงการทั้งหมด <?php echo number_format($total); ?> กิจกรรม
                            </h4>
                        </div>

                        <?php

                        } catch (PDOException $e) {

                            echo "<div class='alert alert-danger'>";
                            echo "Error : " . $e->getMessage();
                            echo "</div>";
                        }

                        ?>

                    </div>

                </div>
            </div>

        </section>

    </div>

    <?php include "./modules/footer.inc.php"; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const ctx = document.getElementById('myChart');

new Chart(ctx, {

    type: 'doughnut',

    data: {

        labels: <?php echo json_encode($labels); ?>,

        datasets: [{

            label: 'จำนวนกิจกรรม',

            data: <?php echo json_encode($data); ?>,

            backgroundColor: [

                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#8BC34A',
                '#E91E63',
                '#009688',
                '#795548',
                '#607D8B'

            ],

            borderColor: '#ffffff',
            borderWidth: 2,
            hoverOffset: 15

        }]
    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                position: 'right',

                labels: {
                    font: {
                        size: 14
                    }
                }
            },

            title: {

                display: true,

                text: 'สรุปข้อมูลรูปแบบโครงการ',

                font: {
                    size: 20
                }
            }

        }

    }

});

</script>

</body>
</html>