<?php

session_start();
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';

$formulario = isset($_POST['formulario']) ? trim($_POST['formulario']) : '';
$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

if ($formulario == 'login' && strlen($usuario) > 0 && strlen($clave) > 0) {
    $bd_link = conecta_db();

    $usuario = str_replace(array("'", '"', ";"), "", rawurldecode($usuario));
    $clave = str_replace(array("'", '"', ";"), "", rawurldecode($clave));
    
    if (Operaciones::comprobar_login($bd_link, $usuario, $clave)) {
        if ($_SESSION['NIVEL_USUARIO'] == 2) {
            header('Location: listado_usuarios.php');
        } else {
            header('Location: dashboard.php');
        }
    } else {
        header('Location: error.php');
    }
} else {
    header('Location: error.php');
}
?>
