<?php
// Simulación de datos
$imagenes = [
    ['ruta' => 'assets/foto1.jpeg', 'likes' => 120],
    ['ruta' => 'assets/foto2.jpeg', 'likes' => 95],
    ['ruta' => 'assets/foto3.jpg', 'likes' => 150],
];

foreach ($imagenes as $imagen) {
    echo '
    <div class="image-card">
        <img src="' . $imagen['ruta'] . '" alt="Imagen Destacada">
        <div class="like-dislike-buttons">
            <button class="btn-like">
                <i class="bi bi-hand-thumbs-up"></i> <!-- Icono de pulgar arriba -->
            </button>
            <button class="btn-dislike">
                <i class="bi bi-hand-thumbs-down"></i> <!-- Icono de pulgar abajo -->
            </button>
        </div>
        <p>Likes: ' . $imagen['likes'] . '</p>
    </div>';
}
?>