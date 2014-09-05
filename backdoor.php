<?php
    error_reporting(0);
    session_start();

    /*/
     * configuración:
     * usuario y clave: se recomienda guardar directamente el resultado de la funcion md5 como string.
     * base_path es una ruta absoluta al directorio principal a controlar.
     * secret es una cadena fija que debes rellenar con caracteres aleatorios.
    /*/
    $config = [ "user" => md5("nsd"), "pass" => md5("mi_clave"), "base_path" => __DIR__, "secret" => "4quydp6aqqubqdv2"];

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
                body, html
                {
                    margin: 0;
                    padding: 0;
                    background: #ecf0f1;
                }
                #contenido, #botonera, #log
                {
                    width: 100%;
                    font-family: arial;
                    margin: 0 auto;
                }
                body header
                {
                    border-bottom: 2px solid #34495E;
                    margin-bottom: 5px;
                    background: #2980B9;
                    overflow: hidden;
                }
                body header input
                {
                    float: left;
                }
                body header a
                {
                    float: right;
                }                
                body section
                {
                    width: 100%;
                }                
                body > form > section > table
                {
                    width: 90%;
                    border: 1px solid #34495E;
                    padding: 0;
                    border-collapse: collapse;
                    margin: 0 5%;
                }
                body > form > section > table th
                {
                    border: 1px solid #34495E;
                    padding: 3px 10px;
                    color: #ECF0F1;
                    font-size: 14px;
                    text-transform: uppercase;
                    background: #2C3E50;
                }
                body > form > section > table td
                {
                    border: 1px solid #34495E;
                    padding: 5px 10px;
                }
                body > form > section > table td
                {
                    border: 1px solid #34495E;
                    padding: 5px 10px;
                }
                .directorio
                {
                    background-image:-webkit-linear-gradient(top,#ccc,#aaa);
                    text-align:center;
                }
                
                .button
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
                .button:hover 
                {
                    box-shadow:#000 0 0 10px;
                    color:#000;
                    cursor:pointer;
                    font-weight:bold
                }
                .button:active
                {
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
        if(!isset($_POST["user"]) || !isset($_POST["pass"]) || md5($_POST["user"]) !== $config["user"] || md5($_POST["pass"]) !== $config["pass"])
        {
    ?>
            <header>
                <div id="error">Error! Debes ser un usuario autorizado.</div>
            </header>
            <section>
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
            </section>
    <?php
        }
        else
        {
            // Logueo correcto, habilitar acceso.
            $_SESSION["secret"] = $config["secret"];
        }
    }

    if(isset($_SESSION["secret"]) && $_SESSION["secret"] == $config["secret"])
    {
        // Estas habilitado.
?>
            <form method="post">
                <header>
                    <input type="submit" value="Eliminar seleccionados" class="button">
                    <a href="?logout=1" class="button">Cerrar & Salir.</a>
                </header>
<?php
    // Si tengo que eliminar elimino.
    if(isset($_POST["datos"]) && ($_POST["datos"] = array_filter($_POST["datos"])))
    {
        echo("<div id=\"log\">
                <dl>");
        foreach($_POST["datos"] as $archivo)
        {
            if(strpos($archivo, "..") !== false)
                echo("Error de seguridad en: '$archivo' (No se permite '..' en el nombre.)<br>");
            elseif(!unlink($config["base_path"]."/".$archivo))
            {
                echo("<dt class=\"error\">Unlink fail:</dt>
                        <dd>$config[base_path]/$archivo</dd>");
                if(!file_put_contents($config["base_path"]."/".$archivo, ""))
                    echo ("<dt class=\"error\">File_put_contents fail:</dt>
                        <dd>$config[base_path]/$archivo</dd>");
                else
                    echo("<dt class=\"warning\">File clear:</dt>
                        <dd>$config[base_path]/$archivo</dd>");
            }
            else
                echo("<dt class=\"ok\">File deleted:</dt>
                        <dd>$config[base_path]/$archivo</dd>");
        }
        echo("</dl>
            </div>");
    }
?>
                <section id="contenido">
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
                </section>
            </form>
<?php
    }
?>
        </body>
    </html>

