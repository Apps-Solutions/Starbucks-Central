<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/errors.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesUsuarios.php';
Operaciones::comprobar_derechos('usuarios');

$respuesta = array('success' => false, 'msg' => '');
$ac = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';

$bd_link = conecta_db();

if ($ac == 'alta_usuario') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $clave = isset($_POST['clave']) ? $_POST['clave'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';

    if (strlen($nombre) == 0 || strlen($usuario) == 0 || strlen($clave) == 0 || !is_numeric($perfil)) {
        print json_encode($respuesta);
        die();
    }

    try {
        OperacionesUsuarios::insertar($bd_link, $nombre, $usuario, $clave, $perfil);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'editar_usuario') {
    $id_usuario = isset($_POST['id']) ? $_POST['id'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $clave = isset($_POST['clave']) ? $_POST['clave'] : '';
    $clave = ($clave == '---clave---') ? '' : $clave;
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';

    if (!is_numeric($id_usuario) || strlen($nombre) == 0 || strlen($usuario) == 0 || !is_numeric($perfil)) {
        print json_encode($respuesta);
        die();
    }

    try {
        OperacionesUsuarios::actualizar($bd_link, $id_usuario, $nombre, $usuario, $clave, $perfil);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'eliminar_usuario') {
    $id_usuario = isset($_GET['id']) ? $_GET['id'] : '';

    if (!is_numeric($id_usuario)) {
        header('Location: listado_usuarios.php');
    }

    try {
        OperacionesUsuarios::eliminar($bd_link, $id_usuario);
    } catch (Exception $exc) {
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    header('Location: listado_usuarios.php');
}
?>
