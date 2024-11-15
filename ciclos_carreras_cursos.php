<?php
require_once "database\conectar_db.php";
require_once "clase_usuario.php";
require_once "clase_carrera.php";
require_once "clase_curso.php";
require_once "clase_ciclo.php";


session_start();
if ($_SESSION['usuario_suspendido'] == 1){
    echo "<h1>Este usuario esta suspendido</h1>";
    echo "<br><a href='login.php'>Ir a inicio</a>";
    exit();
}
if (!isset($_SESSION['rol_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <title>Gestionar Ciclos, Carreras y Cursos</title>
</head>
<header>
    <img src="https://isfdyt24-bue.infd.edu.ar/sitio/wp-content/uploads/2020/07/logo-chico.png" alt="Instituto Superior de Formación Docente y Técnica Nº 24" style="float: left; margin-right: 10px; width: 100px; height: 100px;">
    <p class="header_div_nav-item">Instituto Superior de Formación Docente y Técnica Nº 24</p>
</header>

<body>
    <?php
    if (isset($_GET['mensaje'])) {
        $mensaje = '';
        if ($_GET['mensaje'] == 'ciclo_creado') {
            $titulo = 'Éxito';
            $mensaje = 'Ciclo creado con éxito.';
        } 
        elseif ($_GET['mensaje'] == 'ciclo_editado') {
            $titulo = 'Éxito';
            $mensaje = 'Ciclo editado con éxito.';
        }
        elseif ($_GET['mensaje'] == 'curso_creado') {
            $titulo = 'Éxito';
            $mensaje = 'Curso creado con éxito.';
        }
        elseif ($_GET['mensaje'] == 'curso_editado') {
            $titulo = 'Éxito';
            $mensaje = 'Curso editado con éxito.';
        }
        elseif ($_GET['mensaje'] == 'carrera_creada') {
            $titulo = 'Éxito';
            $mensaje = 'Carrera creada con éxito.';
        }
        elseif ($_GET['mensaje'] == 'carrera_editada') {
            $titulo = 'Éxito';
            $mensaje = 'Carrera editada con éxito.';
        }
        elseif ($_GET['mensaje'] == 'ca_error') {
            $titulo = 'Error';
            $mensaje = 'La carrera ya existe.';
        }
        elseif ($_GET['mensaje'] == 'cl_error') {
            $titulo = 'Error';
            $mensaje = 'El ciclo ya existe.';
        }
        elseif ($_GET['mensaje'] == 'cr_error') {
            $titulo = 'Error';
            $mensaje = 'El curso ya existe.';
        }
        if ($mensaje) {
            echo '
            <div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="modalMensajeLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalMensajeLabel">'.$titulo.'</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            '.$mensaje.'
                        </div>
                    </div>
                </div>
            </div>';
        }
    }
    ?>
    <script>
        $(document).ready(function() {
            $('#modalMensaje').modal('show');
            setTimeout(function() {
                $('#modalMensaje').modal('hide');
            }, 3000);
        });
    </script>

    <!-- CICLOS -->
    <main class="container mt-5">
        <div class="form-row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Ciclos</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearCiclo"><i class="fa-solid fa-plus"> </i> Nuevo ciclo</button>
            </div>
            <input type="text" id="searchCiclos" class="form-control mb-3" placeholder="Buscar ciclo...">
            <table class="table table-sm table-striped table-hover mt-4">
                <thead class="table-primary">
                    <tr>
                        <th>Ciclo Lectivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-active" id="tablaCiclos">
                    <?php
                    $ciclos = CicloLectivo::listarCiclos();
                    foreach ($ciclos as $ciclo) {
                        echo "<tr>";
                        echo "<td>{$ciclo['ciclo']}</td>";
                        echo "<td>
                                <button class='btn btn-info btn-sm btnEditarCiclo' data-id='{$ciclo['ciclo_id']}' data-ciclo='{$ciclo['ciclo']}'><i class='fa-solid fa-pen-to-square'> </i> Editar</button>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <script>
                document.getElementById('searchCiclos').addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let rows = document.querySelectorAll('#tablaCiclos tr');

                    rows.forEach(function(row) {
                        let ciclo = row.querySelector('td').textContent.toLowerCase();
                        if (ciclo.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            </script>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Cursos</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearCurso"><i class="fa-solid fa-plus"> </i> Nuevo curso</button>
            </div>
            <input type="text" id="searchCursos" class="form-control mb-3" placeholder="Buscar curso...">
            <table class="table table-sm table-striped table-hover mt-4">
                <thead class="table-primary">
                    <tr>
                        <th>Cursos disponibles</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-active" id="tablaCursos">
                    <?php
                    $cursos = Curso::listar_Cursos();
                    foreach ($cursos as $curso) {
                        echo "<tr>";
                        echo "<td>{$curso['curso']}</td>";
                        echo "<td>
                                <button class='btn btn-info btn-sm btnEditarCurso' data-id='{$curso['curso_id']}' data-curso='{$curso['curso']}'><i class='fa-solid fa-pen-to-square'> </i> Editar</button>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <script>
                document.getElementById('searchCursos').addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let rows = document.querySelectorAll('#tablaCursos tr');

                    rows.forEach(function(row) {
                        let curso = row.querySelector('td').textContent.toLowerCase();
                        if (curso.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            </script>
        </div>
    </main>


    <!-- Modal Crear Ciclo -->
    <div class="modal fade" id="modalCrearCiclo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formCrearCiclo" action="altas_y_modificaciones\ciclos\crear_ciclo.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nuevo Ciclo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ciclo">Ciclo </label>
                            <input type="number" class="form-control" id="ciclo" name="ciclo" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Ciclo -->
    <div class="modal fade" id="modalEditarCiclo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formEditarCiclo" action="altas_y_modificaciones\ciclos\editar_ciclo.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Ciclo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editar_ciclo_id" name="ciclo_id">
                        <div class="form-group">
                            <label for="editar_ciclo">Nombre </label>
                            <input type="number" class="form-control" id="editar_ciclo" name="ciclo" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
    $(document).ready(function() {
        $('.btnEditarCiclo').on('click', function() {
            var id = $(this).data('id');
            var ciclo = $(this).data('ciclo');
            $('#editar_ciclo_id').val(id);
            $('#editar_ciclo').val(ciclo);
            $('#modalEditarCiclo').modal('show');
        });
    });
    </script>



    <!-- Carreras -->
    <main class="container mt-5">
        <div class="form-row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Carreras</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearCarrera"><i class="fa-solid fa-plus"> </i> Nueva carrera</button>
            </div>
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar carrera...">
            <table class="table table-sm table-striped table-hover mt-4">
                <thead class="table-primary">
                    <tr>
                        <th>Carrera</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-active" id="carreraTable">
                    <?php
                    $carreras = Carrera::listarCarreras();
                    foreach ($carreras as $carrera) {
                        echo "<tr>";
                        echo "<td>{$carrera['carrera_nombre']}</td>";
                        echo "<td>
                                <button class='btn btn-info btn-sm btnEditarCarrera' data-id='{$carrera['carrera_id']}' data-carrera='{$carrera['carrera_nombre']}'><i class='fa-solid fa-pen-to-square'> </i> Editar</button>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById('searchInput').addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll('#carreraTable tr');

                rows.forEach(function(row) {
                    let carrera = row.querySelector('td').textContent.toLowerCase();
                    if (carrera.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        </script>
    </main>


    <!-- Modal Crear Carrera -->
    <div class="modal fade" id="modalCrearCarrera" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formCrearCarrera" action="altas_y_modificaciones\carreras\crear_carrera.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nueva Carrera</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="carrera_nombre">Carrera </label>
                            <input type="text" class="form-control" id="carrera_nombre" name="carrera_nombre" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Carrera -->
    <div class="modal fade" id="modalEditarCarrera" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formEditarCarrera" action="altas_y_modificaciones\carreras\editar_carrera.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Carrera</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editar_carrera_id" name="carrera_id">
                        <div class="form-group">
                            <label for="editar_carrera_nombre">Carrera </label>
                            <input type="text" class="form-control" id="editar_carrera_nombre" name="carrera_nombre" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.btnEditarCarrera').on('click', function() {
                var id = $(this).data('id');
                var carrera = $(this).data('carrera');
                $('#editar_carrera_id').val(id);
                $('#editar_carrera_nombre').val(carrera);
                $('#modalEditarCarrera').modal('show');
            });
        });
    </script>


    <!-- Modal Crear Curso -->
    <div class="modal fade" id="modalCrearCurso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formCrearCurso" action="altas_y_modificaciones\cursos\crear_curso.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nuevo Curso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="curso">Curso </label>
                            <input type="text" class="form-control" id="curso" name="curso" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Curso -->
    <div class="modal fade" id="modalEditarCurso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formEditarCurso" action="altas_y_modificaciones\cursos\editar_curso.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Curso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editar_curso_id" name="curso_id">
                        <div class="form-group">
                            <label for="editar_curso">Curso </label>
                            <input type="text" class="form-control" id="editar_curso" name="curso" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.btnEditarCurso').on('click', function() {
                var id = $(this).data('id');
                var curso = $(this).data('curso');
                $('#editar_curso_id').val(id);
                $('#editar_curso').val(curso);
                $('#modalEditarCurso').modal('show');
            });
        });
    </script>



</body>
<footer>
    <p class="titulos"><i class="fa-solid fa-arrow-right-from-bracket"> </i><a href="logout.php"> Cerrar sesión</a></p><br>
    <p class="titulos"><i class="fa-solid fa-house"> </i><a href="index.php"> Ir a inicio</a></p><br> 
</footer>

</html>
