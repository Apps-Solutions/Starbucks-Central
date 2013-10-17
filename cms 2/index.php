<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <meta content="text/html;charset=utf-8" http-equiv="Content-Type" />
        <title>Reseller</title>

        <style type="text/css">
            @import url("css/inlog.css");
            @import url('css/style_text.css');
            @import url('css/c-grey.css'); /* COLOR FILE CAN CHANGE TO c-blue.ccs, c-grey.ccs, c-orange.ccs, c-purple.ccs or c-red.ccs */
            @import url('css/form.css');
            @import url('css/messages.css');
        </style>

        <script src="js/jquery-1.6.1.min.js" type="text/javascript"></script>

    </head>

    <body>

        <div class="wrapper">

            <div class="container">

                <!--[if !IE]> START TOP <![endif]-->
                <div class="top"></div>
                <!--[if !IE]> END TOP <![endif]-->

                <!--[if !IE]> START LOGIN <![endif]-->
                <div class="box">
                    <div class="title"><h2>Autenticaci&oacute;n</h2></div>
                    <div class="content forms">

                        <form method="post" action="autenticacion.php">

                            <?php if (isset($_SESSION['ERROR']) && $_SESSION['ERROR'] == 'si'){ ?>
                            <div class="message red">
                                Datos de autenticaci&oacute;n incorrectos.
                                <img alt="Close this item" src="gfx/icon-close.gif" />
                            </div>
                            <?php } ?>
                            
                            <div class="row">
                                <div class="half-left">
                                    <label>Usuario:</label>
                                    <input type="text" name="usuario" value="" />
                                </div>

                                <div class="half">
                                    <label>Contrase&ntilde;a:</label>
                                    <input type="password" name="clave" value="" />
                                </div>
                            </div>

                            <div class="row logged">
                                <div style="float:right;padding:0">
                                    <input type="hidden" name="formulario" value="login"/>
                                    <button type="submit"><span>Acceder</span></button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
                <!--[if !IE]> END LOGIN <![endif]-->
            </div>
        </div>

        <script src="js/jquery.pngFix.js" type="text/javascript"></script>
        <script src="js/jquery.sparkbox-select.js" type="text/javascript"></script>
        <script src="js/inlog.js" type="text/javascript"></script>

    </body>
</html>