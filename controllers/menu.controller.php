<?php
/**
 *
 */
class Menu extends ControllerBase{

  function __construct(){
    parent::__construct();
    $this->view->menus = [];
    $this->view->mensaje="";
    // echo "<p>Nuevo controlador Main</p>";
  }

  function render(){
    $menus = $this->model->getMenu();
    $this->view->menus = $menus;
    $this->view->render();
  }

}

 ?>
