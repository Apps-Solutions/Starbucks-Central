<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesTiendas.php';
require_once 'privado/GenerarXML.php';

$respuesta = array('success' => false, 'msg' => '');
$ac = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';

$bd_link = conecta_db();

if ($ac == 'alta_tienda') {

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $codigo_postal = isset($_POST['codigo_postal']) ? $_POST['codigo_postal'] : '';
    $ciudad = isset($_POST['ciudad']) ? $_POST['ciudad'] : '';
    $provincia = isset($_POST['provincia']) ? $_POST['provincia'] : '';
    $zona = isset($_POST['zona']) ? $_POST['zona'] : '';
    $horario = isset($_POST['horario']) ? $_POST['horario'] : array();
    $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : array();
    $latitud = isset($_POST['latitud']) ? $_POST['latitud'] : '';
    $longitud = isset($_POST['longitud']) ? $_POST['longitud'] : '';

    $latitud = strlen($latitud) > 15 ? substr($latitud, 0, 15) : $latitud;
    $longitud = strlen($longitud) > 15 ? substr($longitud, 0, 15) : $longitud;

    $parametros = array(
        'nombre' => $nombre,
        'direccion' => $direccion,
        'codigo_postal' => $codigo_postal,
        'ciudad' => $ciudad,
        'provincia' => $provincia,
        'zona' => $zona,
        'latitud' => $latitud,
        'longitud' => $longitud
    );

    $bd_link->beginTransaction();

    try {

        $id_tienda = OperacionesTiendas::insertar_tienda($bd_link, $parametros);
        if (is_array($servicios) && count($servicios) > 0) {
            OperacionesTiendas::insertar_servicios($bd_link, $id_tienda, $servicios);
        }
        if (is_array($horario) && count($horario) > 0) {
            OperacionesTiendas::insertar_horario($bd_link, $id_tienda, $horario);
        }

        $bd_link->commit();
        GenerarXML::xml_tiendas($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'editar_tienda') {
    $id_tienda = isset($_POST['id']) ? $_POST['id'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $codigo_postal = isset($_POST['codigo_postal']) ? $_POST['codigo_postal'] : '';
    $ciudad = isset($_POST['ciudad']) ? $_POST['ciudad'] : '';
    $provincia = isset($_POST['provincia']) ? $_POST['provincia'] : '';
    $zona = isset($_POST['zona']) ? $_POST['zona'] : '';
    $horario = isset($_POST['horario']) ? $_POST['horario'] : array();
    $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : array();
    $latitud = isset($_POST['latitud']) ? $_POST['latitud'] : '';
    $longitud = isset($_POST['longitud']) ? $_POST['longitud'] : '';

    $latitud = strlen($latitud) > 15 ? substr($latitud, 0, 15) : $latitud;
    $longitud = strlen($longitud) > 15 ? substr($longitud, 0, 15) : $longitud;

    $parametros = array(
        'nombre' => $nombre,
        'direccion' => $direccion,
        'codigo_postal' => $codigo_postal,
        'ciudad' => $ciudad,
        'provincia' => $provincia,
        'zona' => $zona,
        'latitud' => $latitud,
        'longitud' => $longitud
    );

    $bd_link->beginTransaction();

    try {

        OperacionesTiendas::actualizar_tienda($bd_link, $id_tienda, $parametros);
        if (is_array($servicios)) {
            OperacionesTiendas::actualizar_servicios($bd_link, $id_tienda, $servicios);
        }
        if (is_array($horario)) {
            OperacionesTiendas::actualizar_horario($bd_link, $id_tienda, $horario);
        }

        $bd_link->commit();
        GenerarXML::xml_tiendas($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'eliminar_tienda') {
    $id_tienda = isset($_GET['id']) ? $_GET['id'] : '';

    if (!is_numeric($id_tienda)) {
        header('Location: listado_tiendas.php');
    }

    $bd_link->beginTransaction();
    try {
        OperacionesTiendas::eliminar_tienda($bd_link, $id_tienda);
        $bd_link->commit();
        GenerarXML::xml_tiendas($bd_link);
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    header('Location: listado_tiendas.php');
}
?>
