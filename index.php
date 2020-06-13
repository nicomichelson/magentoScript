  
            <!-- salida por pantalla -->
            <!DOCTYPE HTML>
            <html lang="es">
            <head>
                <meta charset="utf-8"/>
                <title>Cosme Fulanito</title>
            </head>
            <body>
            
                <!--Formulario-->
                <form action="index.php" method="POST">
                    <p>Nickname: <input type="text" name="nickname" /></p>
                    
                    <p><input type="submit" name="submit" value="Enviar"/></p>
                    <p><input type="submit" name="guardar" value="Guardar"/></p>
                </form>



            <?php
                include "clases.php";
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
                    
                    if(!empty($_POST['nickname'])){
                        $nickname =$_POST['nickname'];//no importa si es camelcase anda igual

                        

                        //instancio la clase y llamo al metodo mostrarjson
                        //en este caso muestra un array con los datos d los productos
                        //de un vendedor 
                        $resultado = new Productos($nickname);
                        $productos = $resultado->mostraProductos();
                        file_put_contents('publicaciones.json', json_encode($productos));//crea un archivo .json
                        //echo var_dump($productos);
                        //print_r($productos);
                            
                       
                            foreach($productos as $producto  ):
                                echo "<p> <strong>Producto</strong> </p>";
                                foreach($producto as $key => $value ):

                                    switch($key){
                                        case ($key == "id"):
                                            echo "<p> <strong>$key ...  $value</strong> </p>";
                                        break;
                                        case ($key == "imagenesItem"):
                                            echo "<p> <strong>$key</strong> </p>";
                                            foreach($value as $items => $item):
                                                echo "<p> $items ... $item</p>";
                                            endforeach;
                                        break;
                                        default:
                                            echo "<p> $key ...  $value </p>";

                                    }
                                    
                                endforeach;
                                echo "<br>----------------</br>";
                            endforeach;

                    }else{
                            echo 'campo vacio';
                    
                    }
                }    
                        
                        
                        
                            
            
            ?>
            
            </body>
            </html>


