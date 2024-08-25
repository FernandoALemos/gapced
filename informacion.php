<?php
require_once "database/conectar_db.php";
require_once "clase_materia.php";
require_once "clase_usuario.php";
require_once "clase_carrera.php";
require_once "clase_curso.php";
require_once "clase_materia_carrera.php";

session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] <> 1) {
    echo "<h1>Usted no posee permisos para utilizar esta página</h1>";
    echo "<br><a href='login.php'>Ir a inicio</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Bienvenido</title>
</head>
<header>
    <img src="https://isfdyt24-bue.infd.edu.ar/sitio/wp-content/uploads/2020/07/logo-chico.png" alt="Instituto Superior de Formación Docente y Técnica Nº 24" style="float: left; margin-right: 10px; width: 100px; height: 100px;">
    <p class="header_div_nav-item">Instituto Superior de Formación Docente y Técnica Nº 24</p>
</header>
<main>

    <body>
        <section id="Materias" class="divMaterias">
            <div class="divMaterias-cabecera">
                <button class="btn-descargar" onclick="location.href='buscador.php'"><i class="fa-solid fa-arrow-left"></i>Volver</button>
                <div class="btns-space"></div>
                <button class="btn-descargar" onclick="location.href='asignaturas.php'"><i class="fa-solid fa-pen-to-square"></i>Editar</button>
                <div class="btns-space"></div>
                <button class="btn-descargar" onclick="location.href='excel.php'"><i class="fa-solid fa-file-excel"></i>Descargar</button>
            </div>
            <table class="table table-sm table-striped table-hover mt-4">
                <thead class="table-primary">
                    <tr>
                        <th>CICLO</th>
                        <th>CARRERA</th>
                        <th>CURSO</th>
                        <th>MATERIA</th>
                        <th>MODULOS</th>
                        <th>PROFESOR</th>
                        <th>SITUACIÓN DE REVISTA</th>
                        <th>INSCRIPTOS</th>
                        <th>REGULARES</th>
                        <th>ATRASO ACADEMICO</th>
                        <th>RECURSANTES</th>
                        <th>1° PERIODO</th>
                        <th>2° PERIODO</th>
                    </tr>
                </thead>
                <tbody class="table-active">
                    <?php
                    $con = conectar_db();
                    // guardar los valores de los filtros en la sesión
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $_SESSION['filtros'] = $_POST;
                    }

                    // filtros de la sesión
                    $filtros = isset($_SESSION['filtros']) ? $_SESSION['filtros'] : [];

                    //valores de los filtros
                    $ciclo = isset($filtros['ciclo']) ? $filtros['ciclo'] : '';
                    $turno_id = isset($filtros['turno_id']) ? $filtros['turno_id'] : '';
                    $carrera = isset($filtros['carrera_id']) ? $filtros['carrera_id'] : '';
                    $curso = isset($filtros['curso_id']) ? $filtros['curso_id'] : '';
                    $profesor = isset($filtros['profesor_id']) ? $filtros['profesor_id'] : '';

                    // Query en base a lo filtrado
                    $sql = "SELECT 
                                    mc.materia_carrera_id,
                                    m.materia_nombre,
                                    c.carrera_nombre,
                                    cl.ciclo,
                                    cr.curso,
                                    t.turno,
                                    p.profesor_nombre,
                                    p.profesor_apellido,
                                    mc.situacion_revista,
                                    mc.inscriptos,
                                    mc.regulares,
                                    mc.atraso_academico,
                                    mc.recursantes,
                                    mc.modulos,
                                    mc.primer_periodo,
                                    mc.segundo_periodo
                                FROM 
                                    materia_carrera mc
                                JOIN 
                                    materias m ON mc.materia_id = m.materia_id
                                JOIN 
                                    carreras c ON mc.carrera_id = c.carrera_id
                                JOIN 
                                    ciclo_lectivo cl ON mc.ciclo_id = cl.ciclo_id
                                JOIN 
                                    cursos cr ON mc.curso_id = cr.curso_id
                                JOIN 
                                    turnos t ON mc.turno_id = t.turno_id
                                JOIN 
                                    profesores p ON mc.profesor_id = p.profesor_id
                                WHERE 
                                    cl.ciclo = ? AND mc.carrera_id = ? AND mc.turno_id = ?";

                    // Si el curso está seleccionado, agregarlo a la consulta
                    $params = [$ciclo, $carrera, $turno_id];
                    $types = 'iii';
                    // Si el curso está seleccionado, agregarlo a la consulta
                    if (!empty($curso)) {
                        $sql .= " AND mc.curso_id = ?";
                        $params[] = $curso;
                        $types .= 'i';
                    }

                    // Si el profesor está seleccionado, agregarlo a la consulta
                    if (!empty($profesor)) {
                        $sql .= " AND mc.profesor_id = ?";
                        $params[] = $profesor;
                        $types .= 'i';
                    }


                    // Agregar la cláusula ORDER BY para ordenar por materia_nombre y curso
                    $sql .= " ORDER BY cr.curso, m.materia_nombre";

                    // Preparar y ejecutar la consulta
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Obtener el nombre de la carrera
                    $sql_carrera = "SELECT carrera_nombre FROM carreras WHERE carrera_id = ?";
                    $stmt_carrera = $con->prepare($sql_carrera);
                    $stmt_carrera->bind_param("i", $carrera);
                    $stmt_carrera->execute();
                    $stmt_carrera->bind_result($carrera_nombre);
                    $stmt_carrera->fetch();
                    $stmt_carrera->close();

                    // Obtener el nombre del turno
                    $sql_turno = "SELECT turno FROM turnos WHERE turno_id = ?";
                    $stmt_turno = $con->prepare($sql_turno);
                    $stmt_turno->bind_param("i", $turno_id);
                    $stmt_turno->execute();
                    $stmt_turno->bind_result($turno);
                    $stmt_turno->fetch();
                    $stmt_turno->close();

                    // OBTENGO LA DATA?
                    $data = [];
                    $data_ids = [];
                    while ($row = $result->fetch_assoc()) {
                        $data[] = $row;  // Llenas $data con todas las columnas
                        $data_ids[] = $row['materia_carrera_id'];  // Llenas $data_ids solo con los IDs
                    }

                    // Almacenar los IDs en la sesión
                    $_SESSION['materia_carrera_ids'] = $data_ids;

                    // Almacenar los datos en la sesión para el excel
                    $_SESSION['materia_data'] = $data;
                    $_SESSION['ciclo'] = $ciclo;
                    $_SESSION['carrera_nombre'] = $carrera_nombre;
                    $_SESSION['turno'] = $turno;

                    // Verifica si hay resultados
                    if (empty($data)) {
                        echo "<tr><td colspan='13'><b class='bold red'>No hay materias registradas en el sistema</b></td></tr>";
                    } else {
                        // VER BIEN COMO CENTRAR PORQUE AGRANDA MUCHO LA PANTALLA
                        foreach ($data as $info) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($info['ciclo']); ?></td>
                                <td><?php echo htmlspecialchars($info['carrera_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($info['curso']); ?></td>
                                <td><?php echo htmlspecialchars($info['materia_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($info['modulos']); ?></td>
                                <td><?php echo htmlspecialchars($info['profesor_nombre'] . " " . $info['profesor_apellido']); ?></td>
                                <td><?php echo htmlspecialchars($info['situacion_revista']); ?></td>
                                <td><?php echo htmlspecialchars($info['inscriptos']); ?></td>
                                <td><?php echo htmlspecialchars($info['regulares']); ?></td>
                                <td><?php echo htmlspecialchars($info['atraso_academico']); ?></td>
                                <td><?php echo htmlspecialchars($info['recursantes']); ?></td>
                                <td><?php echo htmlspecialchars($info['primer_periodo']); ?></td>
                                <td><?php echo htmlspecialchars($info['segundo_periodo']); ?></td>
                            </tr>
                    <?php }
                    }

                    $stmt->close();
                    $con->close();
                    ?>
                </tbody>
            </table>
        </section>
    </body>
</main>
<footer>
    <p class="titulos"><i class="fa-solid fa-arrow-right-from-bracket"></i><a href="logout.php">Cerrar sesión</a></p><br>
    <p class="titulos"><i class="fa-solid fa-house"></i><a href="index.php">Ir a inicio</a></p><br> <!-- Lo agrego acá, queda mejor que arriba de todo creo. -->
</footer>

</html>