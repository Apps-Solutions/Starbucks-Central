<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/Imagenes.php';
require_once 'privado/OperacionesComida.php';
require_once 'privado/GenerarXML.php';

$respuesta = array('success' => false, 'msg' => '');
$ac = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';

$bd_link = conecta_db();

if ($ac == 'alta_comida') {

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $descripcion_corta = isset($_POST['descripcion_corta']) ? $_POST['descripcion_corta'] : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $imagen_principal = isset($_POST['imagen_principal']) ? $_POST['imagen_principal'] : '';
    $alergenos = isset($_POST['alergenos']) ? $_POST['alergenos'] : array();
    $cafes = isset($_POST['cafes']) ? $_POST['cafes'] : array();
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 2;

    $parametros = array(
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'disponibilidad' => $disponibilidad,
        'estado' => $estado
    );

    $bd_link->beginTransaction();

    try {

        $id_producto = Operaciones::insertar_producto($bd_link, $parametros);
        OperacionesComida::insertar_comida($bd_link, $id_producto, $descripcion_corta, $categoria);
        if (is_array($alergenos) && count($alergenos) > 0) {
            OperacionesComida::insertar_alergenos($bd_link, $id_producto, $alergenos);
        }
        if (is_array($cafes) && count($cafes) > 0) {
            OperacionesComida::insertar_combinaciones($bd_link, $id_producto, $cafes);
        }

        Operaciones::insertar_imagen_principal($bd_link, $id_producto, $imagen_principal);
        Imagenes::eliminar_del_dico($imagen_principal);

        $bd_link->commit();
        GenerarXML::xml_comidas($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'editar_comida') {
    $id_producto = isset($_POST['id']) ? $_POST['id'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $descripcion_corta = isset($_POST['descripcion_corta']) ? $_POST['descripcion_corta'] : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $imagen_principal = isset($_POST['imagen_principal']) ? $_POST['imagen_principal'] : '';
    $imagen_principal_eliminada = isset($_POST['imagen_principal_eliminada']) ? $_POST['imagen_principal_eliminada'] : '';
    $alergenos = isset($_POST['alergenos']) ? $_POST['alergenos'] : array();
    $cafes = isset($_POST['cafes']) ? $_POST['cafes'] : array();
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 2;

    if (!is_numeric($id_producto)) {
        print json_encode($respuesta);
        die();
    }

    $bd_link->beginTransaction();
    try {
        Operaciones::actualizar_producto($bd_link, $id_producto, $nombre, $descripcion, $disponibilidad, $estado);
        OperacionesComida::actualizar_comida($bd_link, $id_producto, $descripcion_corta, $categoria);
        OperacionesComida::actualizar_alergenos($bd_link, $id_producto, $alergenos);
        OperacionesComida::actualizar_combinaciones($bd_link, $id_producto, $cafes);

        if (!is_numeric($imagen_principal)) {
            Operaciones::insertar_imagen_principal($bd_link, $id_producto, $imagen_principal);
        }

        if (is_numeric($imagen_principal_eliminada)) {
            Operaciones::eliminar_imagenes($bd_link, $id_producto, array($imagen_principal_eliminada));
        }

        # Si no ha ocurrido ningun error y se ha subido una nueva imagen principal, eliminamos la imagen temporal
        if (!is_numeric($imagen_principal)) {
            Imagenes::eliminar_del_dico($imagen_principal);
        }

        $bd_link->commit();
        GenerarXML::xml_comidas($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'eliminar_comida') {
    $id_producto = isset($_GET['id']) ? $_GET['id'] : '';

    if (!is_numeric($id_producto)) {
        header('Location: listado_comidas.php');
    }

    $bd_link->beginTransaction();
    try {
        Operaciones::eliminar_producto($bd_link, $id_producto);
        $bd_link->commit();
        GenerarXML::xml_comidas($bd_link);
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    header('Location: listado_comidas.php');
}
?>
