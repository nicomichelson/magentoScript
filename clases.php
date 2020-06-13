<?php

                // Definimos la clase
            class Productos{
                
                //constantes
                const PORCENTAJE_COMISION = 13;
                const GASTOS_ENVIO = 284;
                
                
                private $nickname;
                private $datos_productos;
                
                
                
                // Constructor
                public function __construct($nickname){
                   
                    $this->validarJson($nickname);
                }
                
                //metodos

                private function validarJson($nickname){//valida una api

                    $json = file_get_contents('https://api.mercadolibre.com/sites/MLA/search?nickname='.$nickname.'&offset=0&limit=2');
                    //$json = file_get_contents('https://api.mercadolibre.com/sites/MLA/search?seller_id=127422368&offset=0&limit=5');
                    //LAPARFUMERIE

                    $data = json_decode($json);
                    $this->armarData($data);

                }

                public function armarData($data){//arma un array de salida con productos y sus datos


                    $productos = array();

                    foreach($data->results as $k ):
                         
                        
                        $importe_publicacion = $k->price;
                        $comision = $this->totalComision($k->price);
                        $gananciaML = $this->totalComision($k->price) - $this->gastoEnvioVendedor();
                        $precio_original =$importe_publicacion - $comision - $this->gastoEnvioVendedor();
                        
                        //obtengo item completo por cada publicacion y datos de las imagenes de cada publicacion
                        $item = $this->devolverItem($k->id,$precio_original);
                        
                        $imagenes = $this->devolverImagen($k->id);
                        
                        
                       
                        $producto=[ 'id'            => $k->id, 
                            'title'                 => $k->title,
                            'price'                 => $k->price,
                            'currency_id'           => $k->currency_id, 
                            'available_quantity'    => $k->available_quantity, 
                            'sold_quantity'         => $k->sold_quantity, 
                            'listing_type_id'       => $k->listing_type_id, 
                            'stop_time'             => $k->stop_time,
                            'condition'             => $k->condition, 
                            'permalink'             => $k->permalink, 
                            'thumbnail'             => $k->thumbnail, 
                            'accepts_mercadopago'   => $k->accepts_mercadopago, 
                            'category_id'           => $k->category_id, 
                            'official_store_id'     => $k->official_store_id,
                           
                            // items
                            'precioItem'            =>$item['precio'],
                            'precioBaseItem'        =>$item['precio_base'],
                            'precioOriginalItem'    =>$item['precio_original'],//viene null de la api
                            'miniaturaItem'         =>$item['miniatura'],
                            //precios calculados
                            'total_comision'        =>  $comision,
                            'gananciaML'            =>  $gananciaML,
                            'gastos_envio_vendedror' =>  $this->gastoEnvioVendedor(),
                            'precio_original'        =>  $precio_original,
                             //'imagenesItem'           =>  $imagenes
                            // //quiero listar todo lo q tiene imagenes aca.. 
                            // //imagenes me trae un array como el q necesito
                            // $url = $imagen[valor de tu indice];
                            // $id = $imagen [valor de tu indice];
                            // $size = $imagen [valor de tu indice] 
                        ]; 
                        
                        $resultado=array_merge($producto,$imagenes);
                        
                        array_push($productos, $resultado);
                        
                    endforeach;

                         
                         $this->datos_productos = $productos;
                    
                    
                }

                private function devolverItem($item,$precio_original){//devuelve el item de un producto

                    $json = file_get_contents('https://api.mercadolibre.com/items/'.$item);
                    $data = json_decode($json);
                    
                    
                    $salItem=[
                            'precio'            =>  $data->price,
                            'precio_base'       =>  $data->base_price,
                            'precio_original'   =>  $data->original_price, //viene null de la api
                            'miniatura'         =>  $data->secure_thumbnail,
                            'imagenes'          =>  $data->pictures
                    ];

                    return $salItem;

                }

                public function devolverImagen($imagen){//devuelve las imagenes de cada publicacion
                    $json = file_get_contents('https://api.mercadolibre.com/items/'.$imagen);
                    
                    $data = json_decode($json);
                    
                    $imagenes = $data->pictures;
                    $imagen_datos=[];
                    $id=0;
                    $url=0;
                    $sec=0;
                    $size=0;
                    foreach($imagenes as $imagen):
                        foreach($imagen as $item => $valor):
                            switch($item){
                                case ($item == 'id'):
                                    $imagen_datos['id'.$id] = $valor;
                                    $id++;
                                break;
                                case ($item == 'url'):
                                    $imagen_datos['url'.$url] = $valor;
                                    $url++;
                                break;
                                case($item == 'secure_url'):
                                    $imagen_datos['secure_url'.$sec] = $valor;
                                    $sec++;
                                break;
                                case($item == 'size'):
                                    $imagen_datos['size'.$size] = $valor;
                                    $size++; 
                            }
                        endforeach;
                    endforeach;
                    return $imagen_datos;
                    // foreach($imagenes as $imagen){
                        
                    //     //$id = datanueva [  ' id ' ] = $id; 
                    //     //datanueva [  ' id ' ] => $url; datanueva [  ' url' ] => $url; 
                    //     //$i d= $imagen->id;
                    //     $url = $imagen->url;
                    //     $id = $imagen->id;
                    //     $secure_url = $imagen->secure_url;
                    //     // proceso de url http:\/\/mla-s2-p.mlstatic.com\/823153-MLA42033392380_062020-O.jpg
                        
                    //     $exploded_url = explode("/",$url);
                    //     $url_completa = $exploded_url[0].'//'.$exploded_url[2].'/'.$exploded_url[3];
                    //     //$image_url[]=$url_completa;
                    //     array_push($image_url,$id);
                    //     array_push($image_url,$url_completa);
                    //     array_push($image_url,$secure_url);
                    // }

                    // return $image_url;
                    

                    

                }

                private function gastoEnvioVendedor(){
                   return self::GASTOS_ENVIO/2;
                }

                private function totalComision($precio){//calcula la comision de un producto
                    return ($precio * self::PORCENTAJE_COMISION)/100;
                }

                

                public function mostraProductos(){//devuelve un array con productos
                    
                    return $this->datos_productos;
                }

                
               
                
            }
            ?>