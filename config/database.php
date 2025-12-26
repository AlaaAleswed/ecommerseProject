<?php
return [

    'type' => 'pdo',
    

    'host' => '127.0.0.1',      
    'username' => 'root',      
    'password' => '',           
    'database' => 'ecommerce_db', 
    
  
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
]; 