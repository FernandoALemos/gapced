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
                <button class="btn-descargar" onclick="location.href='buscador.php'">Volver</button>
                <div class="btns-space"></div>
                <button class="btn-descargar">Descargar Excel</button>
            </div>
            <?php echo $_POST['ciclo']." ".$_POST['carrera_id']." ".$_POST['curso_id']." ".$_POST['profesor_id'];?>
            <table class="lista">
                <thead>
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
                <tbody>
                    <?php
                    $con = conectar_db();

                    // Obtener los valores de POST
                    $ciclo = isset($_POST['ciclo']) ? $_POST['ciclo'] : '';
                    $carrera = isset($_POST['carrera_id']) ? $_POST['carrera_id'] : '';
                    $curso = isset($_POST['curso_id']) ? $_POST['curso_id'] : '';
                    $profesor = isset($_POST['profesor_id']) ? $_POST['profesor_id'] : '';

                    // Construir la consulta SQL basada en los filtros seleccionados
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
                                cl.ciclo = ? AND mc.carrera_id = ?";

                    // Si el curso está seleccionado, agregarlo a la consulta
                    $params = [$ciclo, $carrera];
                    $types = 'ii';
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

                    // Preparar y ejecutar la consulta
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Verificar si hay resultados
                    if ($result->num_rows == 0) {
                        echo "<tr><td colspan='13'><b class='bold red'>No hay materia_carrera registradas en el sistema</b></td></tr>";
                    } else {
                        while ($info = $result->fetch_assoc()) { ?>
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
    <font-size="2">
        <h4>
            <p class="titulos"><a href="logout.php">Cerrar sesión</a></p><br>
            <p class="titulos"></p>
        </h4>
    </font-size>
</footer>

</html>