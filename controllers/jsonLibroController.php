<?php
// clase que nos provee del ENDPOINT para trabajar con libros y JSON
class jsonLibroController extends Controller{
    
    //contructor
    public function __construct(){
          header('Content-type:application/json; charset=utf-8');    
    }
    
    public function  get($param1 = NULL, $param2 = NULL){
        
        switch(true){
            
            case $param1 && $param2: // dos parametro es una búsqueda filtrada
                $libros = Libro::getFiltered($param1, $param2);
                break;
                
            case $param1 && !$param2: // un parámetros es una búsqueda por ID
                if(!$libro = Libro::getById($param1)){
                    http_response_code(400);
                    throw new ApiException("no se encontró el libro $param1");
                    
                }
                $libros = [$libro];
                break;
                
                // si no hay parámetros se se recuperan todos
            default: $libros = Libro::get();
            
        }
        //preparar la repuesta
        $response = new stdClass();
        $response->status = "OK";
        $response->message = "Se han recuperado " .sizeof($libros)." resultados.";
        $response->results = sizeof($libros);
        $response->data = $libros;
        
        // retorna el resultado pasado a JSON
        echo JSON::encode($response);
    }
    public function post(){
        
        // recuperar los datos en crudo en el body de la petición
        $json = $this->request->body();
        
        if(empty($json)) //si no llegan los datos
            throw new ApiException('No se indicaron libros a insertar');
            
             //convierte el JSON recibido en php
             // en caso de que json tenga errores de sintaxis se lanzará una excepción
             $libros = JSON::decode($json, 'Libro');
             
             //prepara un objeto para la repuesta
             $response = new stdClass();
             $response->status  = "OK";
             $response->message = "";
             $response->data = [];
    
    }
}
           
       
       foreach($libros as $libro){
              $libro->saneate();
              $errores = $libro->validate(); //valida
              
              if(sizeof($errores)){
                  $response->status = "WARNING";
                  $response->message .= "$libro->titulo tiene errores. ";
                  $response->data[$libro->titulo] = $errores;
              }else{
                  try{
                      $libro->save();
                      $response->message .= "$libro->titulo guardado corectamente. ";
                      http_response_code(201); //código de respuesta será 201
                  }catch(Throwable $t){
                      $response->status = "WARNING";
                      $response->message .= "$libro->titulo no se pudo guardar. ";
                      $response->data[$libro->titulo] = DEBUG ? $t->getMessage():" Duplicado?";
                  }
              }
       }
       echo  JSON::encode($response); //retorna la respuesta en json

     //Actualizar un libro
     
      

