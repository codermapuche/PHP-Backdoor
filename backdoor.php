<?php
    error_reporting(0);
    session_start();

    /*/
     * configuración:
     * usuario y clave: se recomienda guardar directamente el resultado de la funcion md5 como string.
     * base_path es una ruta absoluta al directorio principal a controlar.
     * secret es una cadena fija que debes rellenar con caracteres aleatorios.
    /*/
    $config = [ "user" => md5("nsd"), "pass" => md5("mi_clave"), "base_path" => "/", "secret" => "4quydp6aqqubqdv2"];
    
    // Salir.
    if(isset($_GET["logout"]) && $_GET["logout"] == 1)
    {
        $_SESSION = [];
        if (ini_GET("session.use_cookies")) {
            $params = session_GET_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        unset($params);
    }
?>
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8" />
            <title>Panel de control de archivos</title>
            <style>
                /* Estilos visuales de la pagina embebidos */
                body
                {
                    background-image:-webkit-linear-gradient(top,#f9f9f9,#e9e9e9);
                }
                #contenido , #botonera , #log
                {
                    width:900px;
                    border:3px double #666;
                    padding:10px;
                    color:#222;
                    font-family:arial;
                    font-size:12px;
                    font-variant:small-caps;
                    font-weight:bold;
                    margin:10px auto;
                    box-shadow:rgba(0,0,0,0.498039) 0 1px 3px,rgba(255,255,255,0.701961) 0 0 1px inset;
                    background-image:-webkit-gradient(linear,0 0%,0 100%,from(#0e4168),color-stop(0.1,#236ba3),color-stop(0.5,#236ba3),color-stop(0.9,#236ba3),to(#0e4168));
                    min-height:40px;
                    border-radius:5px;
                    text-align:center;
                }
                form td , form table
                {
                    border:1px solid #666;
                    padding:10px;
                    color:#222;
                    font-family:arial;
                    font-size:15px;
                    font-variant:small-caps;
                    font-weight:bold;
                    margin:10px auto;
                    box-shadow:rgba(0,0,0,0.498039) 0 1px 3px,rgba(255,255,255,0.701961) 0 0 1px inset;
                    background-image:-webkit-linear-gradient(top,#fefefe,#ededed);
                    border-radius:3px
                }
                form table
                {
                    width:875px;
                    margin:5px auto;
                    border-radius:6px
                }
                th
                {
                    border:1px solid #666;
                    padding:10px;
                    color:#eee;
                    font-family:arial;
                    font-size:20px;
                    font-variant:small-caps;
                    font-weight:bold;
                    margin:10px auto;
                    box-shadow:rgba(0,0,0,0.498039) 0 1px 3px,rgba(255,255,255,0.701961) 0 0 1px inset;
                    background-image:-webkit-linear-gradient(top,#555,#111);
                    border-radius:3px;
                    text-align:center;
                }
                .directorio
                {
                    background-image:-webkit-linear-gradient(top,#ccc,#aaa);
                    text-align:center;
                }
                input[type=button] , input[type=submit] , .button
                {
                    background-image:-webkit-gradient(linear,0 0%,0 100%,from(#fff),color-stop(0.25,#ebebeb),color-stop(0.5,#dbdbdb),to(#b5b5b5));
                    border:1px solid #949494;
                    border-bottom-left-radius:3px;
                    border-bottom-right-radius:3px;
                    border-top-left-radius:3px;
                    border-top-right-radius:3px;
                    box-shadow:rgba(0,0,0,0.498039) 0 1px 3px,#fff 0 0 2px inset;
                    color:#333;
                    font-family:arial;
                    font-size:14px;
                    font-weight:bold;
                    margin:5px;
                    padding:5px 20px;
                    text-shadow:rgba(0,0,0,0.2) 0 -1px 0px,#fff 0 1px 0;
                    font-variant:small-caps;
                }
                input[type=button]:hover , input[type=submit]:hover , .button:hover {
                    box-shadow:#000 0 0 10px;
                    color:#000;
                    cursor:pointer;
                    font-weight:bold
                }
                input[type=button]:active , input[type=submit]:active , .button:active{
                    box-shadow:#efefef 0 0 10px
                }
                #error{
                    background-image:-webkit-gradient(linear,0 0%,0 100%,from(#ea5347),to(#d66124));
                    border:1px solid #959595;
                    border-bottom-left-radius:3px;
                    border-bottom-right-radius:3px;
                    border-top-left-radius:3px;
                    border-top-right-radius:3px;
                    box-shadow:#000 0 0 8px;
                    color:#222;
                    font-size:14px;
                    font-variant:small-caps;
                    margin:30px auto;
                    min-height:50px;
                    padding:25px 0 0 0;
                    text-align:center;
                    text-shadow:#959595 0 1px 0;
                    width:300px;
                }
            </style>
        </head>
        <body>
    <?php
    // Validar que este autorizado el acceso.
    if(!isset($_SESSION["secret"]) || $_SESSION["secret"] != $config["secret"])
    {
        // Verificar usuario y clave.
        if(!isset($_POST["user"]) || !isset($_POST["user"]) || md5($_POST["user"]) !== $config["user"] || md5($_POST["pass"]) !== $config["pass"])
        {
    ?>
            <div id="botonera">
                <div id="error">Error! Debes ser un usuario autorizado.</div>
            </div>
            <div id="contenido">
                <form id="formulario" method="post" action="?login=1">
                    <table>
                        <tr>
                            <td>Usuario:</td>
                            <td><input type="text" name="user"></td>
                        </tr>
                        <tr>
                            <td>Clave: </td>
                            <td><input type="password" name="pass"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Entrar!"></td>
                        </tr>
                    </table>
                </form>
            </div>
    <?php
        }
        else
        {
            // Logueo correcto, habilitar acceso.
            $_SESSION["secret"] = $config["secret"];

            // Si tu server lo permite, puedes reemplazar este echo por un: header("location: $_SERVER[REQUEST_URI]");
    ?>
            <div id="botonera">
                <a href="<?=$_SERVER["REQUEST_URI"];?>" class="button">Acceder!</a>
            </div>
    <?php
        }
    }
    else
    {
        // Estas habilitado.
?>
            <form id="formulario" method="post">
                <div id="botonera">
                    <input type="submit" value="eliminar seleccionados" style="float:left" />
                    <a href="?logout=1" class="button" style="float:right" >Cerrar & Salir.</a>
                </div>
                <div id="log">
<?php
    // Si tengo que eliminar elimino.
    if(isset($_POST["datos"]) && ($_POST["datos"] = array_filter($_POST["datos"])))
    {
        foreach($_POST["datos"] as $archivo)
        {
            if(strpos($archivo, "..") !== false)
                echo("Error de seguridad en: '$archivo' (No se permite '..' en el nombre.)<br>");
            elseif(!unlink($config["base_path"].$archivo))
                echo ("Error de sistema borrando: $archivo (No se pudo eliminar el archivo mediante unlink.)<br>");
            else
                echo ("Archivo borrado: $archivo<br>");
        }
    }
?>
                </div>
                <div id="contenido">
                    <table>
                        <thead>
                            <tr>
                                <th> Selec. </th> <th> Path </th> <th> Archivo </th>
                            </tr>
                        </thead>                        
<?php
    function listararchivos($base_path, $root)
    {
        if(is_dir($base_path)) 
        {
            if($dir = opendir($base_path)) 
            {
                while(($archivo = readdir($dir))) 
                {
                    if(($archivo != ".") && ($archivo != ".."))
                    {
                        if(is_dir($base_path.$archivo))
                        {
                            echo '<tr><td colspan="3" class="directorio" >'.$archivo.'</td></tr>';
                            listararchivos($base_path.$archivo.'/', $root);
                        }
                        else
                        {
                            echo('<tr>
                                    <td><input type="checkbox" name="datos[]" value="'.substr($base_path.$archivo, strlen($root)).'"/></td>
                                    <td>'.$base_path.'</td>
                                    <td>'.$archivo.'</td>
                                  </tr>');
                        }
                    }
                }
                closedir($dir);
            }
        }
        else        
            echo "Error de sistema: '$base_path' (No es ruta valida)<br>";
    }
    
    // Muestro los archivos del directorio permitido.
    listararchivos($config["base_path"], $config["base_path"]);
?>
                    </table>
                </div>
            </form>
<?php 
    }
?>
        </body>
    </html>
 
