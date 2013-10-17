<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesUsuarios.php';
Operaciones::comprobar_derechos('form_add_usuario');
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
$listado_perfiles = OperacionesUsuarios::listado_perfiles($bd_link);
?>

<script type="text/javascript">

    function validateEmail(email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if( !emailReg.test( email ) ) {
            return false;
        } else {
            return true;
        }
    }

    $(document).ready(function() {

        $("#guardar").button();
        $("#guardar").click(function() {
            var nombre = jQuery.trim($('#nombre').val());
            var usuario = jQuery.trim($('#usuario').val());
            var clave = jQuery.trim($('#clave').val());
            var perfil = jQuery.trim($('#perfil').val());

            if (nombre.length == 0){
                alert('El campo nombre es obligatorio.');
                return false;
            }else if (usuario.length == 0){
                alert('El campo usuario es obligatorio.');
                return false;
            }else if (!validateEmail(usuario)){
                alert('El usuario debe ser una dirección de correo electrónico.');
                return false;
            }else if (clave.length == 0){
                alert('El campo contraseña es obligatorio.');
                return false;
            }else if (clave.length < 6){
                alert('La contraseña debe tener al menos 6 caracteres.');
                return false;
            }

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "usuarios.php",
                data: {
                    nombre: nombre,
                    usuario: usuario,
                    clave: clave,
                    perfil: perfil,
                    accion: 'alta_usuario'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'listado_usuarios.php';
                }else{
                    alert('No ha sido posible dar de alta el usuario');
                }
            });

            return false;
        });

        $('#perfil').val(3);
    });

</script>

<div class="holder">

    <table style="border: 1px solid #D3D3D3;width: 98%;margin: 10px;padding: 20px">
        <tr>
            <td colspan="2" style="text-align: center;font-weight: bold;font-size: 1.3em;padding-bottom: 30px"><span class="titulo_formulario">Formulario de alta de usuario</span></td>
        </tr>

        <tr>
            <td class="etiqueta_campo" style="width: 200px">Nombre <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre" name="nombre" type="text" style="width:200px"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Usuario (correo electr&oacute;nico) <span class="color_campo_obligatorio">*</span></td>
            <td><input id="usuario" name="usuario" type="text" style="width:200px"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Contrase&ntilde;a <span class="color_campo_obligatorio">*</span></td>
            <td><input id="clave" name="clave" type="password" style="width:200px"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Perfil <span class="color_campo_obligatorio">*</span></td>
            <td>
                <select id="perfil" name="perfil">
                    <?php foreach ($listado_perfiles as $perfil): ?>
                        <option value="<?php print $perfil['FIIDPERFIL']; ?>"><?php print $perfil['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="color_campo_obligatorio" style="text-align: right">* Obligatorio</td>
        </tr>

        <tr>
            <td></td>
            <td><br/><button id="guardar">Guardar</button><img id="loader_guardando" src="gfx/images/ajax-loader.gif" style="display: none" /><br/><br/></td>
        </tr>

    </table>

</div>

<?php require_once 'includes/pie.php'; ?>
