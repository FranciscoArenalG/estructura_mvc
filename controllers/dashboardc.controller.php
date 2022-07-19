<?php
/**
 *
 */
class Dashboardc extends ControllerBase{

  function __construct(){
    parent::__construct();
    // Estructura 4 - Aplica para ... y General
    if (isset($_SESSION['id_usuario-'.constant('Sistema')]) && $_SESSION['fk_estructura-'.constant('Sistema')] != 4) {
      header("location:".constant('URL')."Errores");
    }
  }

  function render(){
    $this->view->render('dashboardc/index');
  }

  function saludo(){
    echo "<p>Ejecutaste el método saludo</p>";
  }
}

 ?>
