<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesUsuarios.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('listado_usuarios');
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
$listado_usuarios = OperacionesUsuarios::listado_usuarios($bd_link);
?>

<script type="text/javascript">

    function confirmacion(){
        if(!confirm("Por favor, confirme que desea eliminar el usuario.")) {
            return false;
        }
    }

    $(document).ready(function() {
        $('#tabla_listado').dataTable({
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "oLanguage": {
                "sProcessing":   "Procesando...",
                "sLengthMenu":   "Mostrar _MENU_ registros",
                "sZeroRecords":  "No se encontraron resultados",
                "sInfo":         "Mostrando desde _START_ hasta _END_ de _TOTAL_ registros",
                "sInfoEmpty":    "Mostrando desde 0 hasta 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros en total)",
                "sInfoPostFix":  "",
                "sSearch":       "Buscar:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sPrevious": "Anterior",
                    "sNext":     "Siguiente",
                    "sLast":     "Último"
                }
            }
        });
    } );

</script>

<div class="holder">

    <div cellpadding="0" cellspacing="0" border="0" class="display" style="width:800px;margin-left: auto;margin-right: auto; margin-top: 30px">
        <table id="tabla_listado" >
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Perfil</th>
                    <th>Fecha alta</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listado_usuarios as $usuario) {
                ?>
                    <tr>
                        <td><?php print $usuario['nombre'] ?></td>
                        <td><?php print $usuario['usuario'] ?></td>
                        <td style="text-align: center"><?php print $usuario['perfil'] ?></td>
                        <td style="width:130px;text-align: center"><?php print $usuario['fecha'] ?></td>
                        <td style="width:50px;text-align: center">
                            <a href="form_editar_usuario.php?id=<?php print $usuario['id_usuario']; ?>"><img src="gfx/images/editar_16.png" alt="Editar usuario" /></a> &nbsp;
                            <a href="usuarios.php?accion=eliminar_usuario&id=<?php print $usuario['id_usuario']; ?>"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar usuario" onclick="return confirmacion(this);"/></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/pie.php'; ?>