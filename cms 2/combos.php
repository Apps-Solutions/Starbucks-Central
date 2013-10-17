<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesCafes.php';
require_once 'privado/OperacionesComida.php';
require_once 'privado/OperacionesBebidas.php';
require_once 'privado/OperacionesTiendas.php';
require_once 'privado/GenerarXML.php';

$ac = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';
$bd_link = conecta_db();

if ($ac == 'datos_combo') {
    $respuesta = array('success' => false, 'datos' => array(), 'msg_error' => '');
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

    if ($tipo == 'disponibilidad_todo') {
        $listado_disponibilidad = Operaciones::listado_disponibilidad($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_disponibilidad;
    } else if ($tipo == 'perfiles_cafe') {
        $listado_perfiles = OperacionesCafes::listado_perfiles($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_perfiles;
    } else if ($tipo == 'formas_cafe') {
        $listado_formas = OperacionesCafes::listado_formas($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_formas;
    } else if ($tipo == 'sabores_cafe') {
        $listado_sabores = OperacionesCafes::listado_sabores($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_sabores;
    } else if ($tipo == 'alergenos_comida') {
        $listado_alergenos = OperacionesComida::listado_alergenos($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_alergenos;
    } else if ($tipo == 'categorias_comida') {
        $listado_categorias = OperacionesComida::listado_categorias($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_categorias;
    } else if ($tipo == 'servicios_tienda') {
        $listado_servicios = OperacionesTiendas::listado_servicios($bd_link);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_servicios;
    } else if ($tipo == 'categorias_tipo_bebida') {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $listado_categorias = OperacionesBebidas::listado_categorias($bd_link, $id);
        $respuesta['success'] = true;
        $respuesta['datos'] = $listado_categorias;
    }

    print json_encode($respuesta);
} else if ($ac == 'alta_opcion') {
    $respuesta = array('success' => false, 'msg_error' => '');
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

    if (strlen($nombre) == 0) {
        print json_encode($respuesta);
        die();
    }

    try {
        if ($tipo == 'disponibilidad_todo') {
            Operaciones::insertar_disponibilidad($bd_link, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'perfiles_cafe') {
            OperacionesCafes::insertar_perfil($bd_link, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'formas_cafe') {
            OperacionesCafes::insertar_forma($bd_link, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'sabores_cafe') {
            OperacionesCafes::insertar_sabor($bd_link, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'alergenos_comida') {
            OperacionesComida::insertar_alergeno($bd_link, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'categorias_comida') {
            OperacionesComida::insertar_categoria($bd_link, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'servicios_tienda') {
            OperacionesTiendas::insertar_servicio($bd_link, $nombre);
            $respuesta['success'] = true;
        }
    } catch (Exception $exc) {
        
    }

    print json_encode($respuesta);
} else if ($ac == 'alta_categoria_tipo_bebida') {
    $respuesta = array('success' => false, 'msg_error' => '');
    $nombre_categoria = isset($_POST['nombre_categoria']) ? $_POST['nombre_categoria'] : '';
    $subcategorias = isset($_POST['subcategorias']) ? $_POST['subcategorias'] : array();
    $id_tipo_bebida = isset($_POST['id_tipo_bebida']) ? $_POST['id_tipo_bebida'] : '';

    if (strlen($nombre_categoria) == 0 || !is_numeric($id_tipo_bebida)) {
        print json_encode($respuesta);
        die();
    }

    try {
        $id_categoria = OperacionesBebidas::insertar_categoria($bd_link, $nombre_categoria, $id_tipo_bebida);

        foreach ($subcategorias as $nombre_subcategoria) {
            OperacionesBebidas::insertar_categoria($bd_link, $nombre_subcategoria, $id_tipo_bebida, $id_categoria);
        }

        $respuesta['success'] = true;
    } catch (Exception $exc) {
        
    }

    print json_encode($respuesta);
} else if ($ac == 'eliminar_opcion') {
    $respuesta = array('success' => false, 'msg_error' => '');
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

    if (!is_numeric($id)) {
        header('Location: administracion_combos.php?combo=' . $tipo);
        die();
    }

    try {
        if ($tipo == 'disponibilidad_todo') {
            Operaciones::eliminar_disponibilidad($bd_link, $id);
        } else if ($tipo == 'perfiles_cafe') {
            OperacionesCafes::eliminar_perfil($bd_link, $id);
        } else if ($tipo == 'formas_cafe') {
            OperacionesCafes::eliminar_forma($bd_link, $id);
        } else if ($tipo == 'sabores_cafe') {
            OperacionesCafes::eliminar_sabor($bd_link, $id);
        } else if ($tipo == 'alergenos_comida') {
            OperacionesComida::eliminar_alergeno($bd_link, $id);
        } else if ($tipo == 'categorias_comida') {
            OperacionesComida::eliminar_categoria($bd_link, $id);
        } else if ($tipo == 'servicios_tienda') {
            OperacionesTiendas::eliminar_servicio($bd_link, $id);
        }
    } catch (Exception $exc) {

    }

    header('Location: administracion_combos.php?combo=' . $tipo);
    die();
} else if ($ac == 'eliminar_categoria_tipos_bebidas') {
    $respuesta = array('success' => false, 'msg_error' => '');
    $tipo_bebida = isset($_GET['tipo_bebida']) ? trim($_GET['tipo_bebida']) : '';
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';

    try {
        OperacionesBebidas::eliminar_categoria($bd_link, $id);
    } catch (Exception $exc) {
        
    }

    header('Location: administracion_combos.php?combo=tipos_bebida&id_tipo_bebida=' . $tipo_bebida);
} else if ($ac == 'actualizar_opcion') {
    $respuesta = array('success' => false, 'msg_error' => '');
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

    if (strlen($nombre) == 0 || !is_numeric($id)) {
        print json_encode($respuesta);
        die();
    }

    try {
        if ($tipo == 'disponibilidad_todo') {
            Operaciones::actualizar_disponibilidad($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'perfiles_cafe') {
            OperacionesCafes::actualizar_perfil($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'formas_cafe') {
            OperacionesCafes::actualizar_forma($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'sabores_cafe') {
            OperacionesCafes::actualizar_sabor($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'alergenos_comida') {
            OperacionesComida::actualizar_alergeno($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'categorias_comida') {
            OperacionesComida::actualizar_categoria($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        } else if ($tipo == 'servicios_tienda') {
            OperacionesTiendas::actualizar_servicio($bd_link, $id, $nombre);
            $respuesta['success'] = true;
        }
    } catch (Exception $exc) {

    }

    print json_encode($respuesta);
}
?>
