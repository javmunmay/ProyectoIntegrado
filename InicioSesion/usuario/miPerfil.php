<?php
// Verificar si la sesión ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Configuración de caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
  header("Location: InicioSesion.php");
  exit();
}

// Conectar a la base de datos (usando un archivo de conexión común)
include '../php/conexion.php';

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Consulta para obtener la información del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Consulta para obtener el número de certificados del usuario
$sql_certificados = "SELECT COUNT(*) as total_certificados FROM certificados WHERE usuario_id = ?";
$stmt_certificados = $conn->prepare($sql_certificados);
$stmt_certificados->bind_param("i", $user_id);
$stmt_certificados->execute();
$result_certificados = $stmt_certificados->get_result();
$row_certificados = $result_certificados->fetch_assoc(); // Número de certificados

// Guardar el número de certificados en una variable
$total_certificados = $row_certificados['total_certificados'];

// Consulta para obtener el número de certificados del usuario
$sql_certificados = "SELECT COUNT(*) as total_certificados FROM certificados WHERE usuario_id = ?";
$stmt_certificados = $conn->prepare($sql_certificados);
$stmt_certificados->bind_param("i", $user_id);
$stmt_certificados->execute();
$result_certificados = $stmt_certificados->get_result();
$row_certificados = $result_certificados->fetch_assoc(); // Número de certificados

// Guardar el número de certificados en una variable
$total_certificados = $row_certificados['total_certificados'];

// Consulta para obtener el número de cursos únicos en progreso del usuario
$sql_cursos_en_progreso = "SELECT COUNT(DISTINCT curso_id) as total_cursos_en_progreso 
                           FROM progreso_usuarios 
                           WHERE usuario_id = ? AND estado = 'en_progreso'";
$stmt_cursos_en_progreso = $conn->prepare($sql_cursos_en_progreso);
$stmt_cursos_en_progreso->bind_param("i", $user_id);
$stmt_cursos_en_progreso->execute();
$result_cursos_en_progreso = $stmt_cursos_en_progreso->get_result();
$row_cursos_en_progreso = $result_cursos_en_progreso->fetch_assoc(); // Número de cursos en progreso

// Guardar el número de cursos en progreso en una variable
$total_cursos_en_progreso = $row_cursos_en_progreso['total_cursos_en_progreso'];


// Consulta para obtener la información de suscripción del usuario
$sql_suscripcion = "SELECT * FROM suscripciones WHERE id_usuario = ? ORDER BY fecha_fin DESC LIMIT 1";
$stmt_suscripcion = $conn->prepare($sql_suscripcion);
$stmt_suscripcion->bind_param("i", $user_id);
$stmt_suscripcion->execute();
$result_suscripcion = $stmt_suscripcion->get_result();
$suscripcion = $result_suscripcion->fetch_assoc();

// Determinar el estado de la suscripción
$estado_suscripcion = "No suscrito"; // Valor por defecto
$clase_estado = "text-secondary";
$proximo_pago = "No aplica";
$tipo_plan = "No aplica";
$fecha_fin_formatted = null;

if ($suscripcion) {
  $fecha_fin = new DateTime($suscripcion['fecha_fin']);
  $hoy = new DateTime();

  // Estado viene directamente de la base de datos (asegurando mayúscula inicial)
  $estado_suscripcion = ucfirst(strtolower($suscripcion['estado'])); // Normaliza a "Activo" o "Cancelado"

  // Asignar clase CSS según estado
  if ($estado_suscripcion == "Activo") {
    $clase_estado = "text-success";
    // Calcular próximo pago solo si está activo
    $proximo_pago = clone $fecha_fin;
    $proximo_pago->modify('+1 day')->format('Y-m-d');
  } elseif ($estado_suscripcion == "Cancelado") {
    $clase_estado = "text-danger";
  } else {
    $clase_estado = "text-warning";
  }

  $tipo_plan = $suscripcion['tipo_plan'] ?? "Mensual";
  $fecha_fin_formatted = $fecha_fin->format('Y-m-d');
}

$stmt_suscripcion->close();


// Cerrar las consultas
$stmt->close();
$stmt_certificados->close();
$stmt_cursos_en_progreso->close();

// Cerrar la conexión
$conn->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Información Personal | Estudiante Programador</title>
  <link rel="stylesheet" href="../css/cssinfoPersonal.css" />
  <link rel="icon" type="image/png" href="../imagenes/favicon.ico" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
  <header>
    <nav>
      <img
        src="../imagenes/LogoFondoAzul.jpg"
        alt="Logo Estudiante Programador"
        class="logo" />
      <button class="menu-toggle" aria-label="Abrir menú">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <ul class="nav-links">
        <li><a href="MiAcademia.php">Inicio</a></li>
        <li>
          <a href="InformacionPersonal.php">Mi Perfil</a>
        </li>
        <li><a href="CursosDentroSesion.php">Cursos</a></li>
        <li><a href="MisCertificados.php">Mis Certificados</a></li>
        <li><a href="ContactoSesionIniciada.php">Ayuda</a></li>
        <li>
          <a class="cerrarSesion" href="../php/logout.php">Cerrar Sesión</a>
        </li>
      </ul>
    </nav>
  </header>



  <main>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Información</h5>
            <a href="InformacionPersonal.php" class="btn btn-close"></a>
          </div>
          <div class="modal-body">
            <form id="editForm" onsubmit="guardarCambios(event)">
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" name="telefono">
              </div>
              <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
              </div>
              <button type="submit" class="btn" style="background-color: #090643; border-color: #090643; color: white;">Guardar Cambios</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section class="hero-banner bg-light p-2 mb-4 mt-4 text-center">
      <h1 class="text-center mb-4 mt-4" style="font-weight: bold; color: #090643;">Información Personal</h1>
    </section>

    <!-- Sección de Información Personal -->
    <section id="info-personal" class="hero-banner">
      <div class="banner-content">
        <!-- Foto de perfil -->
        <div class="foto-perfil-container">
          <img src="../ImagenesDeUsuarios/<?php echo $row['foto_perfil'] ?? '../Usuario.jpg'; ?>"
            alt="Foto de Perfil" class="foto-perfil" />

          <!-- Formulario para subir una nueva imagen -->
          <form id="form-imagen" action="../php/actualizar_imagen.php" method="post" enctype="multipart/form-data">
            <label for="nueva_imagen" class="cambiar-imagen-label btn btn-primary mb-2" style="background-color: #090643; border-color: #090643;">
              <i class="fas fa-camera"></i> Subir Nueva Imagen
            </label>
            <input type="file" id="nueva_imagen" name="nueva_imagen" accept="image/*" style="display: none;">
          </form>

          <!-- Botón para restablecer la imagen predeterminada -->
          <form action="../php/restablecer_imagen.php" method="post" class="restablecer-form">
            <button type="submit" class="btn btn-secondary btn-sm restablecer-btn" style="transform: scale(0.95);">
              <i class="fas fa-undo"></i> Cambiar Imagen Existente
            </button>
          </form>
        </div>

        <!-- Información del usuario -->
        <div class="info-usuario">
          <p class="mt-4"><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($row['nombre']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
          <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($row['numero_telefono']); ?></p>
          <p><strong>Edad:</strong>
            <?php
            $fechaNacimiento = new DateTime($row['fecha_nacimiento']);
            $hoy = new DateTime();
            echo $hoy->diff($fechaNacimiento)->y;
            ?>
            años
          </p>
          <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($row['fecha_nacimiento']); ?></p>
          <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($row['fecha_registro']); ?></p>
          <p>
            <strong>Aceptó Política de Privacidad:</strong>
            <?php echo $row['politica_privacidad'] ? 'Sí' : 'No'; ?>
          </p>
          <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#editModal"
            style="background-color: #090643; border-color: #090643;" onclick="editarInformacion()">
            Actualizar Información
          </button>
        </div>
      </div>
    </section>

    <!-- Sección con Progreso, Preferencias, etc. -->
    <section class="hero-banner">
      <div class="banner-content">
        <h3>Preferencias de Cuenta</h3>
        <p><strong>Idioma:</strong> Español</p>
        <p id="estadoNotificaciones">Notificaciones: <span id="estadoTexto" style="color: green; font-weight: bold;">Activadas</span></p>
        <button class="btn btn-primary btn-lg" style="background-color: #090643; border-color: #090643;" onclick="configurarPreferencias()">Configurar Preferencias</button>
      </div>
    </section>

    <!-- Sección de Seguridad -->
    <section id="seguridad" class="hero-banner">
      <div class="banner-content">
        <h2>Seguridad</h2>
        <p class="mt-3" style="color: green; font-weight: bold;">Se le redirigirá fuera de la sesión para actualizar su contraseña.</p>
        <a class="btn btn-primary btn-lg" style="background-color: #090643; border-color: #090643;" href="../ContenidoPrincipal/Contacto.php#recuperarcontrasena">Cambiar Contraseña</a>

        <h3 class="mt-5">Consejos de Seguridad</h3>

        <p><button class="btn btn-primary btn-lg mt-3" style="background-color: #090643; border-color: #090643;" data-bs-toggle="modal" data-bs-target="#consejosSeguridadModal">Consejos de Seguridad</button></p>

      </div>
    </section>



    <!-- Modal -->
    <div class="modal fade" id="consejosSeguridadModal" tabindex="-1" aria-labelledby="consejosSeguridadModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <!-- Encabezado del modal -->
          <div class="modal-header">
            <h5 class="modal-title" id="consejosSeguridadModalLabel">Consejos de Seguridad</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <!-- Cuerpo del modal -->
          <div class="modal-body">
            <h5>1. Protege tu contraseña</h5>
            <p>
              Usa contraseñas fuertes y únicas para cada cuenta. Evita usar información personal como tu nombre o fecha de nacimiento.
            </p>
            <h5>2. Cierra Sesión siempre</h5>
            <p>
              Cuando termines de usar una aplicación o sitio web, cierra sesión para evitar que otras personas accedan a tu cuenta.
            </p>
            <h5>3. Mantén tu software actualizado</h5>
            <p>
              Asegúrate de que tu sistema operativo, navegador y aplicaciones estén siempre actualizados para evitar vulnerabilidades.
            </p>
            <h5>4. No compartas información personal</h5>
            <p>
              Evita compartir información sensible como contraseñas, números de tarjetas de crédito o datos personales en línea.
            </p>
            <h5>5. Cuidado con los correos sospechosos</h5>
            <p>
              No abras enlaces ni descargues archivos de correos electrónicos no solicitados o de remitentes desconocidos.
            </p>
          </div>
          <!-- Pie del modal -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>




    <!-- Sección Certificados y Logros -->
    <section id="certificados-logros" class="hero-banner">
      <div class="banner-content">
        <h2>Certificados y Logros</h2>
        <?php if ($total_certificados == 0): ?>
          <p>Aún no tienes certificados para mostrar.</p>
        <?php elseif ($total_certificados == 1): ?>
          <p style="color: green; font-weight: bold;">Ha obtenido <?php echo $total_certificados; ?> certificado. ¡Enhorabuena, su primer certificado!</p>
        <?php else: ?>
          <p style="color: green; font-weight: bold;">Ha obtenido <?php echo $total_certificados; ?> certificados, ¡enhorabuena!</p>
        <?php endif; ?>

        <h2 class="mt-5">Progreso en Cursos</h2>
        <?php if ($total_certificados): ?>
          <p style="color: green; font-weight: bold;">Certificados obtenidos: <?php echo $total_certificados ?></p>
        <?php else: ?>
          <p>Certificados obtenidos: 0</p>
        <?php endif; ?>

        <?php if ($total_cursos_en_progreso): ?>
          <p style="color: orange; font-weight: bold;">Cursos en Progreso: <?php echo $total_cursos_en_progreso ?></p>
        <?php else: ?>
          <p>Cursos en Progreso: 0</p>
        <?php endif; ?>

        <a class="btn btn-primary btn-lg" style="background-color: #090643; border-color: #090643;" href="MisCertificados.php">Ver Certificados</a>
      </div>
    </section>

    <!-- Sección Suscripciones y Pagos -->
    <section id="suscripciones-pagos" class="hero-banner">
      <div class="banner-content">
        <h2>Suscripción</h2>
        <div class="suscripcion-info mb-4">
          <h6>Información de tu suscripción:</h6>
          <p><strong>Tipo:</strong> <?php echo htmlspecialchars($tipo_plan ?? 'No suscrito'); ?></p>
          <p><strong>Estado:</strong> <span class="<?php echo $clase_estado; ?>"><?php echo $estado_suscripcion; ?></span></p>
          <?php if (isset($fecha_fin_formatted)): ?>
            <p><strong>Válida hasta:</strong> <?php echo htmlspecialchars($fecha_fin_formatted); ?></p>
          <?php endif; ?>
        </div>

        <?php if ($estado_suscripcion == 'Activo'): ?>
          <button class="btn btn-primary btn-lg"
            style="background-color: #090643; border-color: #090643;"
            data-bs-toggle="modal"
            data-bs-target="#gestionSuscripcionModal">
            Gestionar Suscripción
          </button>
        <?php else: ?>
          <a href="../InicioSesion/ElegirPlan.php?id_usuario=<?php echo $user_id; ?>" class="btn btn-success btn-lg"
            style="background-color: #28a745; border-color: #28a745;">
            Suscribirse
          </a>
        <?php endif; ?>
      </div>
    </section>

    <!-- Modal de Gestionar Suscripción -->
    <div class="modal fade" id="gestionSuscripcionModal" tabindex="-1" aria-labelledby="gestionSuscripcionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="gestionSuscripcionModalLabel">Gestionar Suscripción</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="suscripcion-info mb-4">
              <h6>Información de tu suscripción:</h6>
              <p><strong>Tipo:</strong> <?php echo htmlspecialchars($tipo_plan ?? 'No suscrito'); ?></p>
              <?php if (isset($fecha_fin_formatted)):
                // Convertir la fecha a objeto DateTime y añadir 1 día
                $proximo_pago = new DateTime($fecha_fin_formatted);
                $proximo_pago->modify('+1 day');
                $proximo_pago_formatted = $proximo_pago->format('Y-m-d');
              ?>
                <p><strong>Próximo pago:</strong> <?php echo htmlspecialchars($proximo_pago_formatted); ?></p>
              <?php endif; ?>
              <p><strong>Estado:</strong> <span class="<?php echo $clase_estado; ?>"><?php echo $estado_suscripcion; ?></span></p>
            </div>

            <div class="alert alert-warning border-start border-5 border-warning">
              <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-circle me-3 mt-1"></i>
                <div>
                  <p class="fw-bold mb-2">¿Estás seguro de cancelar tu suscripción?</p>
                  <ul class="ps-3 mb-1">
                    <li class="mb-2">Mantendrás todos los beneficios hasta el <strong><?php echo htmlspecialchars($fecha_fin_formatted); ?></strong></li>
                    <li class="mb-2">No se realizarán más cargos automáticos</li>
                    <li>Si decides volver a suscribirte posteriormente, deberás aceptar las condiciones y precios vigentes en ese momento (que pueden diferir de tu plan actual)</li>
                  </ul>
                </div>
              </div>
            </div>

            <form id="cancelarSuscripcionForm" action="../php/cancelar_suscripcion.php" method="post">
              <div class="form-group mb-3">
                <label for="razonCancelacion" class="form-label">¿Por qué deseas cancelar? (Opcional)</label>
                <select class="form-select" id="razonCancelacion" name="razonCancelacion">
                  <option value="">Selecciona una razón...</option>

                  <!-- Razones económicas -->
                  <optgroup label="Motivos económicos">
                    <option value="precio_alto">El precio es demasiado alto</option>
                    <option value="cambio_situacion">Mi situación económica ha cambiado</option>
                    <option value="no_presupuesto">No lo tengo en mi presupuesto actual</option>
                  </optgroup>

                  <!-- Uso del servicio -->
                  <optgroup label="Uso del servicio">
                    <option value="no_uso">No uso el servicio lo suficiente</option>
                    <option value="no_cumple_expectativas">No cumple mis expectativas</option>
                    <option value="contenido_insuficiente">El contenido no es suficiente</option>
                  </optgroup>

                  <!-- Problemas técnicos -->
                  <optgroup label="Experiencia de usuario">
                    <option value="problemas_tecnicos">Problemas técnicos frecuentes</option>
                    <option value="interfaz_compleja">La plataforma es difícil de usar</option>
                    <option value="falta_funcionalidades">Faltan funciones importantes</option>
                  </optgroup>

                  <!-- Alternativas -->
                  <optgroup label="Otras opciones">
                    <option value="encontre_alternativa">Encontré otra plataforma</option>
                    <option value="solo_temporal">Solo necesitaba el servicio temporalmente</option>
                  </optgroup>

                  <!-- Otros -->
                  <option value="otro">Otra razón (especificar abajo)</option>
                </select>
              </div>

              <div class="form-group mb-3" id="comentarioGroup" style="display: none;">
                <label for="comentarioCancelacion" class="form-label">Por favor, explícanos más:</label>
                <textarea class="form-control" id="comentarioCancelacion" name="comentarioCancelacion" rows="3"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
            <button type="button" class="btn btn-danger" id="confirmarCancelacionBtn">Cancelar Suscripción</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación de Cancelación -->
    <div class="modal fade" id="confirmacionCancelacionModal" tabindex="-1" aria-labelledby="confirmacionCancelacionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmacionCancelacionModalLabel">Confirmar Cancelación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de que deseas cancelar tu suscripción?</p>
            <p class="text-danger"><strong>Nota:</strong> Tendrás acceso hasta el final del periodo actual.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, conservar suscripción</button>
            <button type="button" class="btn btn-danger" id="finalizarCancelacionBtn">Sí, cancelar suscripción</button>
          </div>
        </div>
      </div>
    </div>

    <section class="hero-banner">
      <div class="cerrarSesion">
        <a class="btn btn-danger" href="../php/logout.php">Cerrar Sesión</a>
      </div>
    </section>

  </main>

  <?php include '../php/footerSesion.php'; ?>

  <script>
    function editarInformacion() {
      // Obtener los datos del usuario desde PHP
      const nombre = "<?php echo htmlspecialchars($row['nombre']); ?>";
      const email = "<?php echo htmlspecialchars($row['email']); ?>";
      const telefono = "<?php echo htmlspecialchars($row['numero_telefono']); ?>";
      const fechaNacimiento = "<?php echo htmlspecialchars($row['fecha_nacimiento']); ?>";

      // Rellenar los inputs del formulario con los datos del usuario
      document.getElementById('nombre').value = nombre;
      document.getElementById('email').value = email;
      document.getElementById('telefono').value = telefono;
      document.getElementById('fecha_nacimiento').value = fechaNacimiento;

      // Mostrar el modal usando Bootstrap
      const editModal = new bootstrap.Modal(document.getElementById('editModal'));
      editModal.show();
    }

    // Función para cerrar el modal
    function cerrarModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    // Función para enviar los datos del formulario
    function guardarCambios(event) {
      event.preventDefault(); // Evita que el formulario se envíe de forma tradicional

      // Obtener los datos del formulario
      const formData = new FormData(document.getElementById('editForm'));

      // Enviar los datos al servidor usando Fetch API
      fetch('../php/actualizar_usuario.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Cerrar el modal
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            editModal.hide();
            // Recargar la página para mostrar los cambios
            location.reload();
          } else {
            alert('Error al actualizar la información: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }




    let notificationsEnabled = false;

    function configurarPreferencias() {
      notificationsEnabled = !notificationsEnabled;

      const estadoTexto = document.getElementById('estadoTexto');

      if (!notificationsEnabled) {
        estadoTexto.textContent = 'Activadas';
        estadoTexto.style.color = 'green'; // Cambiar a verde si están activadas
        estadoTexto.style.fontWeight = 'bold'; // Aplicar negrita
      } else {
        estadoTexto.textContent = 'Desactivadas';
        estadoTexto.style.color = 'red'; // Cambiar a rojo si están desactivadas
        estadoTexto.style.fontWeight = 'bold'; // Aplicar negrita
      }
    }
  </script>


  <script>
    document.getElementById('nueva_imagen').addEventListener('change', function() {
      document.getElementById('form-imagen').submit();
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mostrar/ocultar campo de comentario cuando se selecciona "Otra razón"
      document.getElementById('razonCancelacion').addEventListener('change', function() {
        const comentarioGroup = document.getElementById('comentarioGroup');
        comentarioGroup.style.display = (this.value === 'otro') ? 'block' : 'none';
      });

      // Manejar el botón de confirmación
      document.getElementById('confirmarCancelacionBtn').addEventListener('click', function() {
        // Validar si se seleccionó "Otra razón" pero no se completó el comentario
        const razon = document.getElementById('razonCancelacion').value;
        const comentario = document.getElementById('comentarioCancelacion').value;

        if (razon === 'otro' && comentario.trim() === '') {
          alert('Por favor, explica tu razón para cancelar.');
          return;
        }

        // Mostrar modal de confirmación
        const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionCancelacionModal'));
        confirmacionModal.show();
      });

      // Manejar el botón de confirmación final
      document.getElementById('finalizarCancelacionBtn').addEventListener('click', function() {
        // Enviar el formulario
        document.getElementById('cancelarSuscripcionForm').submit();
      });
    });
  </script>

  <!-- Bootstrap JS y dependencias -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  <script src="../js/AppInfoCurso.js?v=1.0"></script>
  <script>
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });
  </script>
</body>

</html>