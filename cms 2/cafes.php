<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/Imagenes.php';
require_once 'privado/OperacionesCafes.php';
require_once 'privado/GenerarXML.php';

$respuesta = array('success' => false, 'msg' => '');
$ac = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';

$bd_link = conecta_db();

if ($ac == 'alta_cafe') {

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';
    $forma = isset($_POST['forma']) ? $_POST['forma'] : '';
    $imagen_principal = isset($_POST['imagen_principal']) ? $_POST['imagen_principal'] : '';
    $imagenes = isset($_POST['imagenes']) ? $_POST['imagenes'] : array();
    $sabores = isset($_POST['sabores']) ? $_POST['sabores'] : array();
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
        OperacionesCafes::insertar_cafe($bd_link, $id_producto, $perfil, $forma);
        if (is_array($sabores) && count($sabores) > 0) {
            OperacionesCafes::insertar_sabores($bd_link, $id_producto, $sabores);
        }
        Operaciones::insertar_imagen_principal($bd_link, $id_producto, $imagen_principal);
        if (is_array($imagenes) && count($imagenes) > 0) {
            OperacionesCafes::insertar_imagenes_galeria($bd_link, $id_producto, $imagenes);
        }

        Imagenes::eliminar_del_dico($imagen_principal);

        if (is_array($imagenes) && count($imagenes) > 0) {
            foreach ($imagenes as $imagen) {
                Imagenes::eliminar_del_dico($imagen);
            }
        }

        $bd_link->commit();
        GenerarXML::xml_cafes($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'editar_cafe') {
    $id_producto = isset($_POST['id']) ? $_POST['id'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';
    $forma = isset($_POST['forma']) ? $_POST['forma'] : '';
    $imagen_principal = isset($_POST['imagen_principal']) ? $_POST['imagen_principal'] : '';
    $imagenes = isset($_POST['imagenes']) ? $_POST['imagenes'] : array();
    $imagenes_eliminadas = isset($_POST['imagenes_eliminadas']) ? $_POST['imagenes_eliminadas'] : array();
    $sabores = isset($_POST['sabores']) ? $_POST['sabores'] : array();
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 2;

    if (!is_numeric($id_producto)) {
        print json_encode($respuesta);
        die();
    }

    $bd_link->beginTransaction();
    try {
        Operaciones::actualizar_producto($bd_link, $id_producto, $nombre, $descripcion, $disponibilidad, $estado);
        OperacionesCafes::actualizar_cafe($bd_link, $id_producto, $perfil, $forma);
        OperacionesCafes::actualizar_sabores($bd_link, $id_producto, $sabores);

        if (!is_numeric($imagen_principal)) {
            Operaciones::insertar_imagen_principal($bd_link, $id_producto, $imagen_principal);
        }

        if (is_array($imagenes) && count($imagenes) > 0) {
            OperacionesCafes::actualizar_imagenes_galeria($bd_link, $id_producto, $imagenes);
        }

        # Finalmente eliminamos de la base de datos las imagenes eliminadas por el usuario
        if (is_array($imagenes_eliminadas) && count($imagenes_eliminadas) > 0) {
            Operaciones::eliminar_imagenes($bd_link, $id_producto, $imagenes_eliminadas);
        }

        # Si no ha ocurrido ningun error eliminamos las imagenes temporales
        if (!is_numeric($imagen_principal)) {
            Imagenes::eliminar_del_dico($imagen_principal);
        }

        foreach ($imagenes as $imagen) {
            if (!is_numeric($imagen)) {
                Imagenes::eliminar_del_dico($imagen);
            }
        }

        $bd_link->commit();
        GenerarXML::xml_cafes($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'eliminar_cafe') {
    $id_producto = isset($_GET['id']) ? $_GET['id'] : '';

    if (!is_numeric($id_producto)) {
        header('Location: listado_cafes.php');
    }

    $bd_link->beginTransaction();
    try {
        Operaciones::eliminar_producto($bd_link, $id_producto);
        $bd_link->commit();
        GenerarXML::xml_cafes($bd_link);
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    header('Location: listado_cafes.php');
}
?>
