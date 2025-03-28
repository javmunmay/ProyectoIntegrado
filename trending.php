<?php
session_start();
require_once 'php/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending - Rally Fotográfico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js para los gráficos-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="css/stylesTrending.css">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trending.php">Trending <i class="bi bi-graph-up"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nuevosTalentos.php">Nuevos Talentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ganadoras">Top Shots</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary" href="InicioSesion/registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary" href="InicioSesion/inicioSesion.php">
                            Iniciar Sesión <i class="bi bi-key"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <section class="hero-trending">
        <div class="container">
            <h1 data-aos="fade-up">Las Fotos en Tendencia</h1>
            <p data-aos="fade-up" data-aos-delay="200">Descubre las imágenes más populares y cómo han evolucionado en el ranking</p>
        </div>
    </section>


    <section class="stats-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4" data-aos="fade-right">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Top 5 Fotos del Mes</h3>
                            <div class="chart-container">
                                <canvas id="topPhotosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4" data-aos="fade-left">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Evolución de Votos</h3>
                            <div class="chart-container">
                                <canvas id="votesEvolutionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="trending-photos py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Fotos que están subiendo</h2>
            <div class="row">
                <?php include 'php/trending_photos.php'; ?>
            </div>
        </div>
    </section>


    <section class="comparison-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4" data-aos="fade-up">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h3 class="card-title">Comparativa Semanal</h3>
                            <div class="chart-container">
                                <canvas id="weeklyComparisonChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <footer class="footer text-center py-4">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Rally Fotográfico. Todos los derechos reservados.</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Datos para los gráficos (que van a ser reemplazados por datos reales de la base de datos)
        const topPhotosData = {
            labels: ['Foto #245', 'Foto #189', 'Foto #312', 'Foto #76', 'Foto #143'],
            datasets: [{
                label: 'Votos',
                data: [1254, 987, 845, 732, 698],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        const votesEvolutionData = {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                    label: 'Foto #245',
                    data: [200, 300, 450, 600, 800, 1000, 1254],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Foto #189',
                    data: [150, 250, 350, 500, 650, 800, 987],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.4,
                    fill: true
                }
            ]
        };

        const weeklyComparisonData = {
            labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            datasets: [{
                    label: 'Votos Totales',
                    data: [3200, 4500, 5100, 6200],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Fotos Subidas',
                    data: [85, 120, 145, 180],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.4,
                    fill: true
                }
            ]
        };

        // Configuración común para los gráficos
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Inicialización de los gráficos
        window.onload = function() {
            // Gráfico de Top Fotos
            const topPhotosCtx = document.getElementById('topPhotosChart').getContext('2d');
            new Chart(topPhotosCtx, {
                type: 'bar',
                data: topPhotosData,
                options: chartOptions
            });

            // Gráfico de Evolución de Votos
            const votesEvolutionCtx = document.getElementById('votesEvolutionChart').getContext('2d');
            new Chart(votesEvolutionCtx, {
                type: 'line',
                data: votesEvolutionData,
                options: chartOptions
            });

            // Gráfico de Comparativa Semanal
            const weeklyComparisonCtx = document.getElementById('weeklyComparisonChart').getContext('2d');
            new Chart(weeklyComparisonCtx, {
                type: 'line',
                data: weeklyComparisonData,
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        };
    </script>
</body>

</html>