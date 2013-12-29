<?php

$app = App::GetApp();
$app->launch();

function __autoload($class_name){
  $directorys = array(
       '',
      'Controller/',
      'Helpers/',
      'Libs/',
      'Exceptions/',
      'Model/',
  );
  
  foreach($directorys as $directory) {
    //see if the file exsists
    if(file_exists($directory.$class_name . '.php')) {
      require_once($directory.$class_name . '.php');
      return;
    }           
  }
}

?>