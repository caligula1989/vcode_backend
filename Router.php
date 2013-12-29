<?php

class Router {

    private $controller;
    private $action;
    private $id;
    private $params;

    function __construct() {
        $this->controller = isset($_GET['controller']) ? $_GET['controller'] : null;
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
        $this->id = isset($_GET['id']) ? $_GET['id'] : null;
        $this->params = $_POST;
    }

    public function getParam($name) {
        return isset($this->params[$name]) ? $this->params[$name] : "";
    }

    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function getId() {
        return $this->id;
    }
}

?>