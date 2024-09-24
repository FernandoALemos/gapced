<?php
require_once "../../database/conectar_db.php";
require_once "../../clase_materia.php"; 

$materia_nombre = isset($_POST['materia_nombre']) ? $_POST['materia_nombre'] : null;
$id = isset($_POST['materia_id']) ? $_POST['materia_id'] : null;

if ($materia_nombre) {
    if (Materia::verificarMateria($materia_nombre)) {
        header("Location: ../../materias.php?mensaje=mat_error");
    } 
    else {
        $materia = new Materia($materia_nombre);
        $materia->modificarMateria($id);
        header('Location: ../../materias.php?mensaje=editado');
    }
} 
else {
    echo "Error: faltan datos de la materia.";
}

?>
