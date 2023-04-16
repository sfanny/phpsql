<?php

/* Clase AuthException 
 *
 * Para distinguir las excepciones de autorización
 *
 * autor: Robert Sallent
 * última revisión: 08/04/2023
 *
 */

    class AuthException extends Exception{
        
        public function __construct(
            string $message = '', 
            int $code = 401, 
            Throwable $previous = NULL
        ){
            parent::__construct($message, $code, $previous);
            header("HTTP/1.1 401 Unauthorized");
        }   
        
    }
    
    