<?php

class GenerarXML {

    public static function xml_versiones(PDO $bd_link) {
        $sql = "SELECT FICAFES, FICOMIDAS, FIBEBIDAS, FITIENDAS FROM taversiones;";
        $result = $bd_link->query($sql);
        $fila = $result->fetch(PDO::FETCH_ASSOC);

        if (file_exists('xml/versiones.xml')) {
            @unlink('xml/versiones.xml');
        }

        $fp = self::abrir_fichero('xml/versiones.xml');
        self::escribir_fichero($fp, '<secciones>');

        self::escribir_fichero($fp, '   <seccion>');
        self::escribir_fichero($fp, '       <nombre>cafes</nombre>');
        self::escribir_fichero($fp, '       <version>' . $fila['FICAFES'] . '</version>');
        self::escribir_fichero($fp, '       <fichero>' . DOMINIO_XML . '/xml/cafes.xml</fichero>');
        self::escribir_fichero($fp, '   </seccion>');

        self::escribir_fichero($fp, '   <seccion>');
        self::escribir_fichero($fp, '       <nombre>comidas</nombre>');
        self::escribir_fichero($fp, '       <version>' . $fila['FICOMIDAS'] . '</version>');
        self::escribir_fichero($fp, '       <fichero>' . DOMINIO_XML . '/xml/comidas.xml</fichero>');
        self::escribir_fichero($fp, '   </seccion>');

        self::escribir_fichero($fp, '   <seccion>');
        self::escribir_fichero($fp, '       <nombre>bebidas</nombre>');
        self::escribir_fichero($fp, '       <version>' . $fila['FIBEBIDAS'] . '</version>');
        self::escribir_fichero($fp, '       <fichero>' . DOMINIO_XML . '/xml/bebidas.xml</fichero>');
        self::escribir_fichero($fp, '   </seccion>');

        self::escribir_fichero($fp, '   <seccion>');
        self::escribir_fichero($fp, '       <nombre>tiendas</nombre>');
        self::escribir_fichero($fp, '       <version>' . $fila['FITIENDAS'] . '</version>');
        self::escribir_fichero($fp, '       <fichero>' . DOMINIO_XML . '/xml/tiendas.xml</fichero>');
        self::escribir_fichero($fp, '   </seccion>');

        self::escribir_fichero($fp, '</secciones>');
        self::cerrar_fichero($fp);
    }

    public static function xml_cafes(PDO $bd_link) {
        if (file_exists('xml/cafes.xml')) {
            @unlink('xml/cafes.xml');
        }

        $fp = self::abrir_fichero('xml/cafes.xml');
        self::escribir_fichero($fp, '<cafes>');

        $cafes = self::informacion_cafe($bd_link);
        foreach ($cafes as $cafe) {
            self::escribir_fichero($fp, '<cafe>');
            self::escribir_fichero($fp, '   <nombre>' . $cafe['NOMBREPRODUCTO'] . '</nombre>');
            self::escribir_fichero($fp, '   <descripcion>' . $cafe['FCDESCRIPCION'] . '</descripcion>');
            self::escribir_fichero($fp, '   <disponibilidad>' . $cafe['NOMBREDISPONIBILIDAD'] . '</disponibilidad>');
            self::escribir_fichero($fp, '   <perfil>' . $cafe['NOMBREPERFIL'] . '</perfil>');
            self::escribir_fichero($fp, '   <forma>' . $cafe['NOMBREFORMA'] . '</forma>');

            $sabores = self::sabores_cafe($bd_link, $cafe['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <sabores>');
            foreach ($sabores as $sabor) {
                self::escribir_fichero($fp, '       <sabor>' . $sabor['NOMBRESABOR'] . '</sabor>');
            }
            self::escribir_fichero($fp, '   </sabores>');

            $ruta_imagen = self::imagen_principal($bd_link, $cafe['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <imagenprincipal>' . $ruta_imagen . '</imagenprincipal>');

            $rutas_imagenes = self::imagenes($bd_link, $cafe['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <imagenes>');
            foreach ($rutas_imagenes as $ruta_imagen) {
                self::escribir_fichero($fp, '       <imagen>' . $ruta_imagen . '</imagen>');
            }
            self::escribir_fichero($fp, '   </imagenes>');

            self::escribir_fichero($fp, '</cafe>');
        }

        self::escribir_fichero($fp, '</cafes>');
        self::cerrar_fichero($fp);

        $sql = "UPDATE taversiones SET FICAFES = (FICAFES + 1);";
        $bd_link->exec($sql);
        self::xml_versiones($bd_link);
    }

    public static function xml_comidas(PDO $bd_link) {
        if (file_exists('xml/comidas.xml')) {
            @unlink('xml/comidas.xml');
        }

        $fp = self::abrir_fichero('xml/comidas.xml');
        self::escribir_fichero($fp, '<comidas>');

        $comidas = self::informacion_comida($bd_link);
        foreach ($comidas as $comida) {
            self::escribir_fichero($fp, '<comida>');
            self::escribir_fichero($fp, '   <nombre>' . $comida['NOMBREPRODUCTO'] . '</nombre>');
            self::escribir_fichero($fp, '   <descripcion>' . $comida['FCDESCRIPCION'] . '</descripcion>');
            self::escribir_fichero($fp, '   <descripcioncorta>' . $comida['FCDESCRIPCIONCORTA'] . '</descripcioncorta>');
            self::escribir_fichero($fp, '   <disponibilidad>' . $comida['NOMBREDISPONIBILIDAD'] . '</disponibilidad>');
            self::escribir_fichero($fp, '   <categoria>' . $comida['NOMBRECATEGORIA'] . '</categoria>');

            $ruta_imagen = self::imagen_principal($bd_link, $comida['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <imagen>' . $ruta_imagen . '</imagen>');

            $alergenos = self::alergenos_comida($bd_link, $comida['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <alergenos>');
            foreach ($alergenos as $alergeno) {
                self::escribir_fichero($fp, '       <alergeno>' . $alergeno['NOMBREALERGENO'] . '</alergeno>');
            }
            self::escribir_fichero($fp, '   </alergenos>');

            $combinaciones = self::combinaciones_comida($bd_link, $comida['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <combinaciones>');
            foreach ($combinaciones as $combinacion) {
                $ruta_imagen = self::imagen_principal($bd_link, $combinacion['FIIDPRODUCTO']);

                self::escribir_fichero($fp, '       <combinacion>');
                self::escribir_fichero($fp, '           <nombre>' . $combinacion['NOMBREPRODUCTO'] . '</nombre>');
                self::escribir_fichero($fp, '           <disponibilidad>' . $combinacion['NOMBREDISPONIBILIDAD'] . '</disponibilidad>');
                self::escribir_fichero($fp, '           <imagen>' . $ruta_imagen . '</imagen>');
                self::escribir_fichero($fp, '       </combinacion>');
            }
            self::escribir_fichero($fp, '   </combinaciones>');

            self::escribir_fichero($fp, '</comida>');
        }

        self::escribir_fichero($fp, '</comidas>');
        self::cerrar_fichero($fp);

        $sql = "UPDATE taversiones SET FICOMIDAS = (FICOMIDAS + 1);";
        $bd_link->exec($sql);
        self::xml_versiones($bd_link);
    }

    public static function xml_bebidas(PDO $bd_link) {
        if (file_exists('xml/bebidas.xml')) {
            @unlink('xml/bebidas.xml');
        }

        $fp = self::abrir_fichero('xml/bebidas.xml');
        self::escribir_fichero($fp, '<bebidas>');

        $bebidas = self::informacion_bebidas($bd_link);
        foreach ($bebidas as $bebida) {
            self::escribir_fichero($fp, '<bebida>');
            self::escribir_fichero($fp, '   <nombre>' . $bebida['NOMBREPRODUCTO'] . '</nombre>');
            self::escribir_fichero($fp, '   <descripcion>' . $bebida['FCDESCRIPCION'] . '</descripcion>');
            self::escribir_fichero($fp, '   <descripcioncorta>' . $bebida['FCDESCRIPCIONCORTA'] . '</descripcioncorta>');
            self::escribir_fichero($fp, '   <disponibilidad>' . $bebida['NOMBREDISPONIBILIDAD'] . '</disponibilidad>');
            self::escribir_fichero($fp, '   <temperatura>' . $bebida['TEMPERATURA'] . '</temperatura>');
            self::escribir_fichero($fp, '   <categoria>' . $bebida['NOMBRECATEGORIA'] . '</categoria>');
            self::escribir_fichero($fp, '   <subcategoria>' . $bebida['NOMBRESUBCATEGORIA'] . '</subcategoria>');
            self::escribir_fichero($fp, '   <marcadodecaf>' . $bebida['FCMARCADODECAF'] . '</marcadodecaf>');
            self::escribir_fichero($fp, '   <marcadoshots>' . $bebida['FCMARCADOSHOTS'] . '</marcadoshots>');
            self::escribir_fichero($fp, '   <marcadojarabe>' . $bebida['FCMARCADOJARABE'] . '</marcadojarabe>');
            self::escribir_fichero($fp, '   <marcadoleche>' . $bebida['FCMARCADOLECHE'] . '</marcadoleche>');
            self::escribir_fichero($fp, '   <marcadopersonalizado>' . $bebida['FCMARCADOPERS'] . '</marcadopersonalizado>');
            self::escribir_fichero($fp, '   <marcadobebida>' . $bebida['FCMARCADOBEBIDA'] . '</marcadobebida>');
            self::escribir_fichero($fp, '   <shot>' . $bebida['FISHOT'] . '</shot>');

            $ruta_imagen = self::imagen_principal($bd_link, $bebida['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <imagen>' . $ruta_imagen . '</imagen>');

            $categorias = self::categorias_ingredientes($bd_link, $bebida['FIIDPRODUCTO']);
            self::escribir_fichero($fp, '   <personalizacion>');
            foreach ($categorias as $categoria) {
                self::escribir_fichero($fp, '       <categoria>');
                self::escribir_fichero($fp, '           <nombre>' . $categoria['NOMBRECATEGORIA'] . '</nombre>');
                self::escribir_fichero($fp, '           <ingredientes>');

                $ingredientes = self::ingredientes_categoria_comida($bd_link, $bebida['FIIDPRODUCTO'], $categoria['FIIDCATEGORIAINGREDIENTE']);
                foreach ($ingredientes as $ingrediente) {
                    self::escribir_fichero($fp, '               <ingrediente>' . $ingrediente['NOMBREINGREDIENTE'] . '</ingrediente>');
                }

                self::escribir_fichero($fp, '           </ingredientes>');

                self::escribir_fichero($fp, '       </categoria>');
            }
            self::escribir_fichero($fp, '   </personalizacion>');

            self::escribir_fichero($fp, '</bebida>');
        }

        self::escribir_fichero($fp, '</bebidas>');
        self::cerrar_fichero($fp);

        $sql = "UPDATE taversiones SET FIBEBIDAS = (FIBEBIDAS + 1);";
        $bd_link->exec($sql);
        self::xml_versiones($bd_link);
    }

    public static function xml_tiendas(PDO $bd_link) {
        if (file_exists('xml/tiendas.xml')) {
            @unlink('xml/tiendas.xml');
        }

        $fp = self::abrir_fichero('xml/tiendas.xml');
        self::escribir_fichero($fp, '<tiendas>');

        $tiendas = self::informacion_tiendas($bd_link);
        foreach ($tiendas as $tienda) {
            self::escribir_fichero($fp, '<tienda>');
            self::escribir_fichero($fp, '   <nombre>' . $tienda['NOMBRETIENDA'] . '</nombre>');
            self::escribir_fichero($fp, '   <direccion>' . $tienda['FCDIRECCION'] . '</direccion>');
            self::escribir_fichero($fp, '   <codigopostal>' . $tienda['FCCODIGOPOSTAL'] . '</codigopostal>');
            self::escribir_fichero($fp, '   <ciudad>' . $tienda['FCCIUDAD'] . '</ciudad>');
            self::escribir_fichero($fp, '   <provincia>' . $tienda['NOMBREESTADO'] . '</provincia>');
            self::escribir_fichero($fp, '   <zona>' . $tienda['FCZONA'] . '</zona>');
            self::escribir_fichero($fp, '   <latitud>' . $tienda['FCLATITUD'] . '</latitud>');
            self::escribir_fichero($fp, '   <longitud>' . $tienda['FCLONGITUD'] . '</longitud>');

            $servicios = self::listado_servicios($bd_link, $tienda['FIIDTIENDA']);
            self::escribir_fichero($fp, '   <servicios>');
            foreach ($servicios as $servicio) {
                self::escribir_fichero($fp, '       <servicio>' . $servicio['NOMBRESERVICIO'] . '</servicio>');
            }
            self::escribir_fichero($fp, '   </servicios>');

            $horario = self::horario_tiendas($bd_link, $tienda['FIIDTIENDA']);
            self::escribir_fichero($fp, '   <horario>');
            if (count($horario) == 0) {
                self::escribir_fichero($fp, '       <dia>');
                self::escribir_fichero($fp, '           <nombre>Cerrado 24 horas</nombre>');
                self::escribir_fichero($fp, '           <horainicio></horainicio>');
                self::escribir_fichero($fp, '           <horafin></horafin>');
                self::escribir_fichero($fp, '       </dia>');
            }

            foreach ($horario as $dia) {
                self::escribir_fichero($fp, '       <dia>');
                self::escribir_fichero($fp, '           <nombre>' . $dia['NOMBREDIA'] . '</nombre>');
                self::escribir_fichero($fp, '           <horainicio>' . $dia['FCHORAMINUTOSINICIO'] . '</horainicio>');
                self::escribir_fichero($fp, '           <horafin>' . $dia['FCHORAMINUTOSFIN'] . '</horafin>');
                self::escribir_fichero($fp, '       </dia>');
            }
            self::escribir_fichero($fp, '   </horario>');

            self::escribir_fichero($fp, '</tienda>');
        }


        self::escribir_fichero($fp, '</tiendas>');
        self::cerrar_fichero($fp);

        $sql = "UPDATE taversiones SET FITIENDAS = (FITIENDAS + 1);";
        $bd_link->exec($sql);
        self::xml_versiones($bd_link);
    }

    public static function horario_tiendas(PDO $bd_link, $id_tienda) {
        $sql = "SELECT tds.FCNOMBRE AS 'NOMBREDIA', th.FCHORAMINUTOSINICIO, th.FCHORAMINUTOSFIN";
        $sql.= " FROM tahorarios th, tadiassemana tds";
        $sql.= " WHERE th.FIIDDIA = tds.FIIDDIA";
        $sql.= " AND th.FIIDTIENDA = " . $id_tienda;
        $sql.= " ORDER BY th.FIIDDIA;";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function listado_servicios(PDO $bd_link, $id_tienda) {
        $sql = "SELECT ts.FCNOMBRE AS 'NOMBRESERVICIO'";
        $sql.= " FROM taservicios ts, taserviciosxtienda tst";
        $sql.= " WHERE ts.FIIDSERVICIO = tst.FIIDSERVICIO";
        $sql.= " AND tst.FIIDTIENDA = " . $id_tienda . ";";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function informacion_tiendas(PDO $bd_link) {
        $sql = "SELECT tt.FIIDTIENDA, tt.FCNOMBRE AS 'NOMBRETIENDA', tt.FCDIRECCION, tt.FCCODIGOPOSTAL, tt.FCCIUDAD, te.FCNOMBRE AS 'NOMBREESTADO', tt.FCZONA, tt.FCLATITUD, tt.FCLONGITUD";
        $sql.= " FROM tatiendas tt, taestados te";
        $sql.= " WHERE tt.FIIDESTADO = te.FIIDESTADO";
        $sql.= " ORDER BY tt.FCNOMBRE";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function ingredientes_categoria_comida(PDO $bd_link, $id_producto, $id_categoria) {
        $sql = "SELECT ti.FCNOMBRE AS 'NOMBREINGREDIENTE'";
        $sql.= " FROM taingredientesxbebida tib, taingredientes ti";
        $sql.= " WHERE tib.FIIDINGREDIENTE = ti.FIIDINGREDIENTE";
        $sql.= " AND tib.FIIDBEBIDA = " . $id_producto;
        $sql.= " AND ti.FIIDCATEGORIAINGREDIENTE = " . $id_categoria;
        $sql.= " ORDER BY ti.FCNOMBRE";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function categorias_ingredientes(PDO $bd_link, $id_producto) {
        $sql = "SELECT DISTINCT ti.FIIDCATEGORIAINGREDIENTE, tci.FCNOMBRE AS 'NOMBRECATEGORIA'";
        $sql.= " FROM taingredientesxbebida tib, taingredientes ti, tacategoriasingredientes tci";
        $sql.= " WHERE tib.FIIDINGREDIENTE = ti.FIIDINGREDIENTE";
        $sql.= " AND ti.FIIDCATEGORIAINGREDIENTE = tci.FIIDCATEGORIAINGREDIENTE";
        $sql.= " AND tib.FIIDBEBIDA = " . $id_producto;
        $sql.= " ORDER BY tci.FCNOMBRE;";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function informacion_bebidas(PDO $bd_link) {
        $sql = "SELECT tp.FIIDPRODUCTO, tp.FCNOMBRE AS 'NOMBREPRODUCTO', tp.FCDESCRIPCION, td.FCNOMBRE AS 'NOMBREDISPONIBILIDAD', te.FCNOMBRE AS 'NOMBREESTADO', tb.FCDESCRIPCIONCORTA, tb.FCMARCADODECAF, tb.FCMARCADOSHOTS, tb.FCMARCADOJARABE, tb.FCMARCADOLECHE, tb.FCMARCADOPERS, tb.FCMARCADOBEBIDA, tb.FISHOT, tb.FIIDCATEGORIABEBIDA";
        $sql.= " FROM taproductos tp, tadisponibilidades td, taestadosproductos te, tabebidas tb";
        $sql.= " WHERE tp.FIIDDISPONIBILIDAD = td.FIIDDISPONIBILIDAD";
        $sql.= " AND tp.FIIDESTADO = te.FIIDESTADO";
        $sql.= " AND tp.FIIDPRODUCTO = tb.FIIDPRODUCTO";
        $sql.= " AND tp.FIIDESTADO = 1";
        $sql.= " ORDER BY tp.FCNOMBRE;";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($filas); $i++) {
            $fila = $filas[$i];

            $sql = "SELECT tcb_hijo.FIIDCATEGORIABEBIDA IDCATHIJO, tcb_hijo.FCNOMBRE NOMBREHIJO, tcb_padre.FIIDCATEGORIABEBIDA IDCATPADRE, tcb_padre.FCNOMBRE NOMBREPADRE, tcb_hijo.FIIDTEMPERATURA";
            $sql.= " FROM tacategoriasbebidas tcb_hijo LEFT JOIN tacategoriasbebidas tcb_padre";
            $sql.= " ON (tcb_hijo.FIIDCATEGORIAPADRE = tcb_padre.FIIDCATEGORIABEBIDA)";
            $sql.= " WHERE tcb_hijo.FIIDCATEGORIABEBIDA = " . $fila['FIIDCATEGORIABEBIDA'] . ";";

            $result = $bd_link->query($sql);
            $fila_categoria = $result->fetch(PDO::FETCH_ASSOC);

            if (strlen($fila_categoria['IDCATPADRE']) == 0) {
                $id_categoria = $fila_categoria['IDCATHIJO'];
                $nombre_categoria = $fila_categoria['NOMBREHIJO'];
            } else {
                $id_categoria = $fila_categoria['IDCATPADRE'];
                $nombre_categoria = $fila_categoria['NOMBREPADRE'];
                $id_subcategoria = $fila_categoria['IDCATHIJO'];
                $nombre_subcategoria = $fila_categoria['NOMBREHIJO'];
            }

            $filas[$i]['NOMBRECATEGORIA'] = $nombre_categoria;
            $filas[$i]['NOMBRESUBCATEGORIA'] = isset($nombre_subcategoria) ? $nombre_subcategoria : '';

            $temperatura = self::temperatura_categoria_comida($bd_link, $fila_categoria['FIIDTEMPERATURA']);
            $filas[$i]['TEMPERATURA'] = $temperatura;
        }

        return $filas;
    }

    public static function temperatura_categoria_comida(PDO $bd_link, $id_temperatura) {
        $sql = "SELECT FCNOMBRE";
        $sql.= " FROM tatemperaturas";
        $sql.= " WHERE FIIDTEMPERATURA = " . $id_temperatura . ";";

        $result = $bd_link->query($sql);
        return $result->fetchColumn();
    }

    public static function combinaciones_comida(PDO $bd_link, $id_producto) {
        $sql = "SELECT tp.FIIDPRODUCTO, tp.FCNOMBRE AS 'NOMBREPRODUCTO', td.FCNOMBRE AS 'NOMBREDISPONIBILIDAD'";
        $sql.= " FROM taproductos tp, tadisponibilidades td, tacombinacionescomidas tcc";
        $sql.= " WHERE tp.FIIDDISPONIBILIDAD = td.FIIDDISPONIBILIDAD";
        $sql.= " AND tp.FIIDPRODUCTO = tcc.FIIDCAFE";
        $sql.= " AND tp.FIIDESTADO = 1";
        $sql.= " AND tcc.FIIDCOMIDA = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function alergenos_comida(PDO $bd_link, $id_producto) {
        $sql = "SELECT ta.FCNOMBRE AS 'NOMBREALERGENO'";
        $sql.= " FROM taalergenos ta, taalergenosxcomida tac";
        $sql.= " WHERE ta.FIIDALERGENO = tac.FIIDALERGENO";
        $sql.= " AND tac.FIIDCOMIDA = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function informacion_comida(PDO $bd_link) {
        $sql = "SELECT tp.FIIDPRODUCTO, tp.FCNOMBRE AS 'NOMBREPRODUCTO', tp.FCDESCRIPCION, td.FCNOMBRE AS 'NOMBREDISPONIBILIDAD', te.FCNOMBRE AS 'NOMBREESTADO', tc.FCDESCRIPCIONCORTA, tcc.FCNOMBRE AS 'NOMBRECATEGORIA'";
        $sql.= " FROM taproductos tp, tadisponibilidades td, taestadosproductos te, tacomidas tc, tacategoriascomidas tcc";
        $sql.= " WHERE tp.FIIDDISPONIBILIDAD = td.FIIDDISPONIBILIDAD";
        $sql.= " AND tp.FIIDESTADO = te.FIIDESTADO";
        $sql.= " AND tp.FIIDPRODUCTO = tc.FIIDPRODUCTO";
        $sql.= " AND tc.FIIDCATEGORIACOMIDA = tcc.FIIDCATEGORIACOMIDA";
        $sql.= " AND tp.FIIDESTADO = 1";
        $sql.= " ORDER BY tp.FCNOMBRE";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function imagen_principal(PDO $bd_link, $id_producto) {
        $ruta_imagen = '';

        $sql = "SELECT FIIDIMAGEN, FBIMAGEN";
        $sql.= " FROM taimagenes";
        $sql.= " WHERE FIORDEN = 1";
        $sql.= " AND FIIDPRODUCTO = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        $fila = $result->fetch(PDO::FETCH_ASSOC);

        if (is_array($fila)) {
            self::guardar_imagen_en_disco($bd_link, $fila['FIIDIMAGEN'], $fila['FBIMAGEN']);
            $ruta_imagen = DOMINIO_XML . '/xml/imagenes/' . $fila['FIIDIMAGEN'] . '.jpg';
        }

        return $ruta_imagen;
    }

    public static function imagenes(PDO $bd_link, $id_producto) {
        $rutas_imagenes = array();

        $sql = "SELECT FIIDIMAGEN, FBIMAGEN";
        $sql.= " FROM taimagenes";
        $sql.= " WHERE FIORDEN != 1";
        $sql.= " AND FIIDPRODUCTO = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        while ($fila = $result->fetch(PDO::FETCH_ASSOC)) {
            self::guardar_imagen_en_disco($bd_link, $fila['FIIDIMAGEN'], $fila['FBIMAGEN']);
            $rutas_imagenes[] = DOMINIO_XML . '/xml/imagenes/' . $fila['FIIDIMAGEN'] . '.jpg';
        }

        return $rutas_imagenes;
    }

    public static function guardar_imagen_en_disco(PDO $bd_link, $id_imagen, $imagen_blob) {
        $ruta_imagen = 'xml/imagenes/' . $id_imagen . '.jpg';

        if (file_exists($ruta_imagen)) {
            @unlink($ruta_imagen);
        }

        $fp = fopen('xml/imagenes/' . $id_imagen . '.jpg', 'a');
        fwrite($fp, $imagen_blob);
        fclose($fp);
    }

    public static function informacion_cafe(PDO $bd_link) {
        $sql = "SELECT tp.FIIDPRODUCTO, tp.FCNOMBRE AS 'NOMBREPRODUCTO', tp.FCDESCRIPCION, td.FCNOMBRE AS 'NOMBREDISPONIBILIDAD', te.FCNOMBRE AS 'NOMBREESTADO', tpe.FCNOMBRE AS 'NOMBREPERFIL', tf.FCNOMBRE AS 'NOMBREFORMA'";
        $sql.= " FROM taproductos tp, tadisponibilidades td, taestadosproductos te, tacafes tc, taperfiles tpe, taformas tf";
        $sql.= " WHERE tp.FIIDDISPONIBILIDAD = td.FIIDDISPONIBILIDAD";
        $sql.= " AND tp.FIIDESTADO = te.FIIDESTADO";
        $sql.= " AND tp.FIIDPRODUCTO = tc.FIIDPRODUCTO";
        $sql.= " AND tc.FIIDPERFIL = tpe.FIIDPERFIL";
        $sql.= " AND tc.FIIDFORMA = tf.FIIDFORMA";
        $sql.= " AND tp.FIIDESTADO = 1";
        $sql.= " ORDER BY tp.FCNOMBRE";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function sabores_cafe(PDO $bd_link, $id_producto) {
        $sql = "SELECT ts.FCNOMBRE AS 'NOMBRESABOR'";
        $sql.= " FROM tasaboresxcafe tsc, tasabores ts";
        $sql.= " WHERE tsc.FIIDSABOR = ts.FIIDSABOR";
        $sql.= " AND tsc.FIIDCAFE = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        $filas = $result->fetchAll(PDO::FETCH_ASSOC);
        return $filas;
    }

    public static function abrir_fichero($ruta_fichero) {
        $fp = fopen($ruta_fichero, 'a');
        return $fp;
    }

    public static function cerrar_fichero($fp) {
        fclose($fp);
    }

    public static function escribir_fichero($fp, $cadena) {
        fwrite($fp, $cadena . "\n");
    }

}

?>
