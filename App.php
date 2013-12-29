<?php

class App {
  
  public $db;
  private static $instance;
  private $router;
  private $config;
  
  public static function GetApp(){
    if(self::$instance == null){
      self::$instance = new App();
    }
    return self::$instance;
  }
  
  public function getSetting($name){
    return isset($this->config[$name]) ? $this->config[$name] : "";
  }
  
  public function getRouter(){
    return $this->router;
  }
  
  private function parseConfig(){
    $this->config = parse_ini_file('config.ini');
  }
  
  private function initDb(){
    $this->db = new mysqli(
      $this->getSetting('dbHost'), 
      $this->getSetting('dbUser'), 
      $this->getSetting("dbPassword"), 
      $this->getSetting("dbName")
    );
    $this->db->set_charset("utf8");
  }
  
  public function launch(){
    $this->parseConfig();
    $this->initDb();
    $this->router = new Router();
    $controller = $this->router->getController();
    $method = $this->router->getAction();
    $instance = new $controller();
    print $instance->$method();
  }
  
}

?>