<?php

require __dir__ . '/lib/base.php';
require __dir__ . '/lib/rb.php';

F3::set('XCACHE', 'FALSE');
F3::set('UI', 'App/Views/');
F3::set('AUTOLOAD', 'App/');
F3::set('APP_NAME', 'AutoDW');

$Routers =  new \_References\Routers;
$AutoDB =  new \_References\AutoDB;

$rb = 


R::setup('mysql:host=localhost;dbname=auto_dw','root','');
    
$AutoDB->connect();
$Routers->Load();
$Routers->API();

F3::run();


?>
