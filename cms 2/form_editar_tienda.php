<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesTiendas.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('form_editar_tienda');
$id_tienda = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!is_numeric($id_tienda)) {
    header('Location: listado_tiendas.php');
    die();
}
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
try {
    $datos_tienda = OperacionesTiendas::informacion_tienda($bd_link, $id_tienda);
    $listado_horario_tienda = OperacionesTiendas::listado_horario($bd_link, $id_tienda);
    $listado_servicios_tiendas = OperacionesTiendas::listado_servicios_tienda($bd_link, $id_tienda);
    $listado_provincias = OperacionesTiendas::listado_provincias($bd_link);
    $listado_dias = OperacionesTiendas::listado_dias($bd_link);
    $listado_servicios = OperacionesTiendas::listado_servicios($bd_link);
} catch (Exception $exc) {
    throw new Exception($exc->getMessage(), $exc->getCode());
    die();
}
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/plugins/gmap/jquery.gmap.min.js"></script>

<script type="text/javascript">

    $(document).ready(function() {
        var latitud = <?php print $datos_tienda['latitud']; ?>;
        var longitud = <?php print $datos_tienda['longitud']; ?>;

        var registrar_evento_eliminar_dia = function(){
            $("#horario > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });
            
            $("#horario > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        var registrar_evento_eliminar_servicio = function(){
            $("#listado_servicios > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });

            $("#listado_servicios > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        $('#mapa').gMap({
            markers: [{latitude: latitud, longitude: longitud}],
            latitude: latitud,
            longitude: longitud,
            zoom:16,
            mapTypeControl: false,
            streetViewControl: false
        });

        $("#add_dia").click(function(){
            var texto_dia = $("#dia option:selected").html();
            var id_dia = $("#dia option:selected").val();
            var texto_hora_desde = $("#hora_desde option:selected").html();
            var hora_desde = $("#hora_desde option:selected").val();
            var texto_minutos_desde = $("#minutos_desde option:selected").html();
            var minutos_desde = $("#minutos_desde option:selected").val();
            var texto_hora_hasta = $("#hora_hasta option:selected").html();
            var hora_hasta = $("#hora_hasta option:selected").val();
            var texto_minutos_hasta = $("#minutos_hasta option:selected").html();
            var minutos_hasta = $("#minutos_hasta option:selected").val();
            var existe = false;

            if (id_dia == 'cerrado'){
                existe = $('#horario > li[iddia="cerrado"]').length;
                if (existe > 0){
                    $("#dia").val("");
                    return false;
                }

                $("#horario").empty();
                $("#horario").append('<li iddia="'+id_dia+'" datosdia="'+id_dia+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto_dia+'</li>');
                registrar_evento_eliminar_dia();
                $("#dia").val("");
                return false;
            }

            if (id_dia == 'todos'){
                $("#horario").empty();

                $("#dia > option").each(function(index) {
                    var texto_dia = $(this).html();
                    var id_dia = $(this).val();

                    if ($.isNumeric(id_dia)){
                        var datosdia = id_dia+'-'+texto_hora_desde+':'+texto_minutos_desde+'-'+texto_hora_hasta+':'+texto_minutos_hasta;
                        var texto = texto_dia+' de '+texto_hora_desde+':'+texto_minutos_desde+' a '+texto_hora_hasta+':'+texto_minutos_hasta;

                        $("#horario").append('<li iddia="'+id_dia+'" datosdia="'+datosdia+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto+'</li>');
                        registrar_evento_eliminar_dia();
                    }
                });

                $("#dia").val("");
                return false;
            }

            if (id_dia == 'lunes-viernes'){
                existe = $('#horario > li[iddia="cerrado"]').length;
                if (existe > 0){
                    $('#horario > li[iddia="cerrado"]').remove();
                }

                $("#dia > option").each(function(index) {
                    var texto_dia = $(this).html();
                    var id_dia = $(this).val();

                    if ($.isNumeric(id_dia) && id_dia <= 5){
                        existe = $('#horario > li[iddia="'+id_dia+'"]').length;
                        if (existe > 0){
                            $('#horario > li[iddia="'+id_dia+'"]').remove();
                        }

                        var datosdia = id_dia+'-'+texto_hora_desde+':'+texto_minutos_desde+'-'+texto_hora_hasta+':'+texto_minutos_hasta;
                        var texto = texto_dia+' de '+texto_hora_desde+':'+texto_minutos_desde+' a '+texto_hora_hasta+':'+texto_minutos_hasta;

                        $("#horario").append('<li iddia="'+id_dia+'" datosdia="'+datosdia+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto+'</li>');
                        registrar_evento_eliminar_dia();
                    }
                });

                $("#dia").val("");
                return false;
            }

            if (id_dia == 'sabado-domingo'){
                existe = $('#horario > li[iddia="cerrado"]').length;
                if (existe > 0){
                    $('#horario > li[iddia="cerrado"]').remove();
                }

                $("#dia > option").each(function(index) {
                    var texto_dia = $(this).html();
                    var id_dia = $(this).val();

                    var existe = $('#horario > li[iddia="'+id_dia+'"]').length;

                    if ($.isNumeric(id_dia) && id_dia > 5){
                        existe = $('#horario > li[iddia="'+id_dia+'"]').length;
                        if (existe > 0){
                            $('#horario > li[iddia="'+id_dia+'"]').remove();
                        }

                        var datosdia = id_dia+'-'+texto_hora_desde+':'+texto_minutos_desde+'-'+texto_hora_hasta+':'+texto_minutos_hasta;
                        var texto = texto_dia+' de '+texto_hora_desde+':'+texto_minutos_desde+' a '+texto_hora_hasta+':'+texto_minutos_hasta;

                        $("#horario").append('<li iddia="'+id_dia+'" datosdia="'+datosdia+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto+'</li>');
                        registrar_evento_eliminar_dia();
                    }
                });

                $("#dia").val("");
                return false;
            }

            if (!$.isNumeric(id_dia)){
                alert('Debe seleccionar el día de la semana');
                return false;
            }

            existe = $('#horario > li[iddia="cerrado"]').length;
            if (existe > 0){
                $('#horario > li[iddia="cerrado"]').remove();
            }

            existe = $('#horario > li[iddia="'+id_dia+'"]').length;
            if (existe > 0){
                $('#horario > li[iddia="'+id_dia+'"]').remove();
            }

            var datosdia = id_dia+'-'+texto_hora_desde+':'+texto_minutos_desde+'-'+texto_hora_hasta+':'+texto_minutos_hasta;
            var texto = texto_dia+' de '+texto_hora_desde+':'+texto_minutos_desde+' a '+texto_hora_hasta+':'+texto_minutos_hasta;

            $("#horario").append('<li iddia="'+id_dia+'" datosdia="'+datosdia+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto+'</li>');
            registrar_evento_eliminar_dia();

            $("#dia").val("");
            return false;
        });

        $("#provincia").change(function(){
            $('#latitud').val('');
            $('#longitud').val('');
        });

        $("#servicio").change(function(){
            var texto_servicio = $("#servicio option:selected").html();
            var id_servicio = $("#servicio option:selected").val();
            var existe = false;

            if (!$.isNumeric(id_servicio)){
                return false;
            }

            $('#listado_servicios > li').each(function(index) {
                if ($(this).attr('idservicio') == id_servicio){
                    existe = true;
                    return;
                }
            });

            if (!existe){
                $("#listado_servicios").append('<li idservicio="'+id_servicio+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto_servicio+'</li>');
                registrar_evento_eliminar_servicio();
            }

            $("#servicio").val("");
            return false;
        });


        $("#guardar").button();
        $("#validar_en_mapa").button();

        $("#validar_en_mapa").click(function(){
            var direccion = jQuery.trim($('#direccion').val());
            var ciudad = jQuery.trim($('#ciudad').val());
            var provincia = jQuery.trim($('#provincia option:selected').html());
            var latitud = jQuery.trim($('#latitud').val());
            var longitud = jQuery.trim($('#longitud').val());

            if (latitud.length > 0 && longitud.length > 0 && direccion == 0 && ciudad == 0){
                $('#mapa').gMap({
                    markers: [{latitude: latitud, longitude: longitud}],
                    latitude: latitud,
                    longitude: longitud,
                    zoom:16,
                    mapTypeControl: false,
                    streetViewControl: false
                });
                return false;
            }

            if (direccion.length == 0){
                alert('Tiene que especificar la dirección.');
                return false;
            }else if (ciudad.length == 0){
                alert('Tiene que especificar la ciudad.');
                return false;
            }else if (provincia.length == 0){
                alert('Tiene que especificar el estado.');
                return false;
            }

            var direccion_completa = direccion + ', '+ciudad+', '+provincia + ', Mexico';

            $('#mapa').gMap('geocode', direccion_completa,function(position) {
                $('#latitud').val(position.lat());
                $('#longitud').val(position.lng());
            },function(){
                $('#latitud').val('');
                $('#longitud').val('');
                $('#msg_cargando').hide();
            });

            $('#msg_cargando').show();
            $('#mapa').gMap({
                markers: [{address: direccion_completa}],
                address: direccion_completa,
                zoom:16,
                mapTypeControl: false,
                streetViewControl: false,
                onComplete: function(){
                    $('#msg_cargando').hide();
                }
            });

            return false;
        });

        $("#guardar").click(function() {
            var nombre = jQuery.trim($('#nombre').val());
            var direccion = jQuery.trim($('#direccion').val());
            var codigo_postal = jQuery.trim($('#cp').val());
            var ciudad = jQuery.trim($('#ciudad').val());
            var provincia = jQuery.trim($('#provincia').val());
            var zona = jQuery.trim($('#zona').val());
            var latitud = jQuery.trim($('#latitud').val());
            var longitud = jQuery.trim($('#longitud').val());

            var horario = new Array();
            $('#horario > li').each(function(index) {
                horario.push($(this).attr('datosdia'));
            });

            var servicios = new Array();
            $('#listado_servicios > li').each(function(index) {
                servicios.push($(this).attr('idservicio'));
            });

            if (nombre.length == 0){
                alert('El campo nombre es obligatorio.');
                return false;
            }else if (direccion.length == 0){
                alert('El campo dirección es obligatorio.');
                return false;
            }else if (codigo_postal.length == 0){
                alert('El campo código postal es obligatorio.');
                return false;
            }else if (ciudad.length == 0){
                alert('El campo ciudad es obligatorio.');
                return false;
            }else if (provincia.length == 0){
                alert('Debe seleccionar el estado.');
                return false;
            }else if (horario.length == 0){
                alert('Debe indicar el horario');
                return false;
            }else if (latitud.length == 0 || longitud.length == 0){
                alert('Al hacer cambios en la dirección debe revalidar la dirección en el mapa o especificar las coordenadas (latitud y longitud).')
                return false;
            }

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "tiendas.php",
                data: {
                    id: <?php print $datos_tienda['id_tienda']; ?>,
                    nombre: nombre,
                    direccion: direccion,
                    codigo_postal: codigo_postal,
                    ciudad: ciudad,
                    provincia: provincia,
                    zona: zona,
                    horario: horario,
                    servicios: servicios,
                    latitud: latitud,
                    longitud: longitud,
                    accion: 'editar_tienda'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'listado_tiendas.php';
                }else{
                    alert('No ha sido posible dar de alta la tienda');
                }
            });

            return false;
        });

        registrar_evento_eliminar_dia();
        registrar_evento_eliminar_servicio();

        $('#hora_desde').val(6);
        $('#minutos_desde').val(0);
        $('#hora_hasta').val(22);
        $('#minutos_hasta').val(0);

        $('#provincia').val(<?php print $datos_tienda['id_provincia']; ?>);
    });

</script>

<div class="holder">

    <table style="border: 1px solid #D3D3D3;width: 98%;margin: 10px;padding: 20px">
        <tr>
            <td colspan="2" style="text-align: center;font-weight: bold;font-size: 1.3em;padding-bottom: 30px"><span class="titulo_formulario">Formulario de edici&oacute;n de tienda</span></td>
        </tr>
        <tr>
            <td class="etiqueta_campo">Nombre <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre" name="nombre" type="text" style="width:275px" value="<?php print $datos_tienda['nombre']; ?>"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Ubicaci&oacute;n</td>
            <td colspan="2">
                <fieldset style="margin: 0px">
                    <table>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td class="etiqueta_campo">Direcci&oacute;n <span class="color_campo_obligatorio">*</span></td>
                                        <td>
                                            <input id="direccion" type="text" name="direccion" style="width:275px" value="<?php print $datos_tienda['direccion']; ?>" /> &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="etiqueta_campo">C&oacute;digo Postal <span class="color_campo_obligatorio">*</span></td>
                                        <td>
                                            <input id="cp" type="text" name="cp" style="width:50px" value="<?php print $datos_tienda['codigopostal']; ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="etiqueta_campo">Ciudad <span class="color_campo_obligatorio">*</span></td>
                                        <td>
                                            <input id="ciudad" type="text" name="ciudad" style="width:200px" value="<?php print $datos_tienda['ciudad']; ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="etiqueta_campo">Estado <span class="color_campo_obligatorio">*</span></td>
                                        <td>
                                            <select id="provincia" name="provincia">
                                                <option value="">-seleccione estado-</option>
                                                <?php foreach ($listado_provincias as $provincia): ?>
                                                    <option value="<?php print $provincia['FIIDESTADO']; ?>"><?php print $provincia['FCNOMBRE']; ?></option>
                                                <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="etiqueta_campo">Zona</td>
                                            <td>
                                                <input id="zona" type="text" name="zona" style="width:200px" value="<?php print $datos_tienda['zona']; ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="etiqueta_campo">Latitud</td>
                                            <td>
                                                <input id="latitud" type="text" name="latitud" value="<?php print $datos_tienda['latitud']; ?>" style="width:200px"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="etiqueta_campo">Longitud</td>
                                            <td>
                                                <input id="longitud" type="text" name="longitud" value="<?php print $datos_tienda['longitud']; ?>" style="width:200px"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: center;padding-top: 30px">
                                                <button id="validar_en_mapa">Validar la direcci&oacute;n en el mapa</button>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <div id="mapa" style="width:400px;height:280px"></div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <td class="etiqueta_campo">Horario <span class="color_campo_obligatorio">*</span></td>
                <td>
                    <select id="dia" name="dia">
                        <option value="">--seleccione día--</option>
                        <option value="todos">Todos los días</option>
                        <option value="cerrado">Cerrado 24 horas</option>
                        <option value="lunes-viernes">Lunes a Viernes</option>
                        <option value="sabado-domingo">Sábado y Domingo</option>
                    <?php foreach ($listado_dias as $dia): ?>
                                                        <option value="<?php print $dia['FIIDDIA']; ?>"><?php print $dia['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                                                    </select>
                                                    de
                                                    <select id="hora_desde" name="hora_desde">
                    <?php
                                                        for ($inicio = 0; $inicio < 24; $inicio++):
                                                            $hora = $inicio < 10 ? '0' . $inicio : $inicio;
                    ?>
                                                            <option value="<?php print $inicio; ?>"><?php print $hora; ?></option>
                    <?php endfor; ?>
                                                        </select>:<select id="minutos_desde" name="minutos_desde">
                    <?php
                                                            for ($inicio = 0; $inicio < 60; $inicio++):
                                                                if ($inicio % 5 == 0):
                                                                    $hora = $inicio < 10 ? '0' . $inicio : $inicio;
                    ?>
                                                                    <option value="<?php print $inicio; ?>"><?php print $hora; ?></option>
                    <?php
                                                                    endif;
                                                                endfor;
                    ?>
                                                            </select>
                                                            a
                                                            <select id="hora_hasta" name="hora_hasta">
                    <?php
                                                                for ($inicio = 0; $inicio < 24; $inicio++):
                                                                    $hora = $inicio < 10 ? '0' . $inicio : $inicio;
                    ?>
                                                                    <option value="<?php print $inicio; ?>"><?php print $hora; ?></option>
                    <?php endfor; ?>
                                                                </select>:<select id="minutos_hasta" name="minutos_hasta">
                    <?php
                                                                    for ($inicio = 0; $inicio < 60; $inicio++):
                                                                        if ($inicio % 5 == 0):
                                                                            $hora = $inicio < 10 ? '0' . $inicio : $inicio;
                    ?>
                                                                            <option value="<?php print $inicio; ?>"><?php print $hora; ?></option>
                    <?php
                                                                            endif;
                                                                        endfor;
                    ?>
                                                                    </select>
                                                                    <button id="add_dia">A&ntilde;adir</button>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td></td>
                                                                <td>
                                                                    <fieldset style="width: 225px">
                                                                        <legend>Horario</legend>
                                                                        <ul id="horario" class="lista_sin_formato">
                        <?php if (count($listado_horario_tienda) > 0): ?>
                        <?php foreach ($listado_horario_tienda as $horario): ?>
                                                                                <li iddia="<?php print $horario['id_dia']; ?>" datosdia="<?php print $horario['datosdia']; ?>"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> <?php print $horario['texto']; ?></li>
                        <?php endforeach; ?>
                        <?php else: ?>
                                                                                    <li iddia="cerrado" datosdia="cerrado"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> Cerrado 24 horas</li>
                        <?php endif; ?>
                                                                                </ul>
                                                                            </fieldset>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="etiqueta_campo">Servicios</td>
                                                                        <td>
                                                                            <select id="servicio" name="servicio">
                                                                                <option value="">--seleccione servicio--</option>
                    <?php foreach ($listado_servicios as $servicio): ?>
                                                                                        <option value="<?php print $servicio['FIIDSERVICIO']; ?>"><?php print $servicio['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                                                                                    </select>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td></td>
                                                                                <td>
                                                                                    <fieldset style="width: 225px">
                                                                                        <legend>Listado de servicios</legend>
                                                                                        <ul id="listado_servicios" class="lista_sin_formato">
                        <?php foreach ($listado_servicios_tiendas as $servicio_tienda): ?>
                                                                                            <li idservicio="<?php print $servicio_tienda['id_servicio']; ?>"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> <?php print $servicio_tienda['nombre']; ?></li>
                        <?php endforeach; ?>
                                                                                        </ul>
                                                                                    </fieldset>
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
