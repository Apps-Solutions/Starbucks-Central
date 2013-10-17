<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/Operaciones.php';

if ($_SESSION['NIVEL_USUARIO'] == 2) {
    header('Location: listado_usuarios.php');
    die();
}

require_once 'includes/cabecera.php';
?>

<div class="holder">

    <div class="box" style="text-align: center;">
        <div class="content">
            <table style="margin-left: auto;margin-right: auto;" align="center" cellspacing="20">
                <tr>
                    <td>
                        <table>
                            <tr><td class="etiqueta_campo">A&ntilde;adir caf&eacute;</td></tr>
                            <tr><td><a href="form_add_cafe.php" title="A&ntilde;adir caf&eacute;" ><img src="gfx/images/cafe_starbucks.png" /></a></td></tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr><td class="etiqueta_campo">A&ntilde;adir alimento</td></tr>
                            <tr><td><a href="form_add_comida.php" title="A&ntilde;adir alimento"><img src="gfx/images/comida_starbucks.png" /></a></td></tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr><td class="etiqueta_campo">A&ntilde;adir bebida</td></tr>
                            <tr><td><a href="form_add_bebida.php" title="A&ntilde;adir bebida"><img src="gfx/images/bebida_starbucks.png" /></a></td></tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr><td class="etiqueta_campo">A&ntilde;adir tienda</td></tr>
                            <tr><td><a href="form_add_tienda.php" title="A&ntilde;adir tienda"><img src="gfx/images/tienda_starbucks.png" /></a></td></tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</div>

<?php require_once 'includes/pie.php'; ?>