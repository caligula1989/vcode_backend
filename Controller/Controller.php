<?php

class Controller {

  private $cssFiles = array();
  private $jsFiles = array();
  
  protected $css;
  protected $js;
  
  protected function addCssFile($cssFile){
    $this->cssFiles[] = $cssFile;
  }
  protected function addJsFile($jsFile){
    $this->jsFiles[] = $jsFile;
  }
  
  private function buildCss(){
    $str = "";
    foreach($this->cssFiles as $cssFile) {
        $str .= '<link href="'.$cssFile.'" rel="stylesheet" />';
    }
    $this->css = $str;
  }
  
  private function buildJs(){
    $str = "";
    foreach($this->jsFiles as $jsFile) {
        $str .= '<script src="'.$jsFile.'"></script>';
    }
    $this->js = $str;
  }
  
  protected function generateView($viewFile){
    $this->buildCss();
    $this->buildJs();
    include 'View/header.phtml';
    include $viewFile;
    include 'View/footer.phtml';
  }

}

?>