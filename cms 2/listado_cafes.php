<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesCafes.php';
require_once 'privado/conecta_db.php';
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
$listado_cafes = OperacionesCafes::listado_cafes($bd_link);
?>

<script type="text/javascript">

    function confirmacion(){
        if(!confirm("Por favor, confirme que desea eliminar el café.")) {
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
                    <th>Descripci&oacute;n</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listado_cafes as $cafe) {
                ?>
                    <tr>
                        <td><?php print $cafe['nombre'] ?></td>
                        <td><?php print $cafe['descripcion'] ?></td>
                        <td style="width:100px;text-align: center"><?php print $cafe['id_estado'] == 1 ? 'Activado' : 'Desactivado' ?></td>
                        <td style="width:50px;text-align: center">
                            <a href="form_editar_cafe.php?id=<?php print $cafe['id_producto']; ?>"><img src="gfx/images/editar_16.png" alt="Editar caf&eacute;" /></a> &nbsp;
                            <a href="cafes.php?accion=eliminar_cafe&id=<?php print $cafe['id_producto']; ?>"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar caf&eacute" onclick="return confirmacion(this);"/></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/pie.php'; ?>