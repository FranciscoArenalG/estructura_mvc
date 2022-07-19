<?php
/**
 *
 */
class ViewBase{

  function __construct(){
    // echo "<p>Vista base</p>";
  }

  function render($vista){
    if ($vista != "login/index") {
      if (!isset($_SESSION['id_usuario-'.constant('Sistema')])) {
        header("location:".constant('URL'));
      }else {
        require("views/".$vista.".view.php");
      }
    }else {
      require("views/login/index.view.php");
    }
    // require("views/".$vista.".view.php");
  }
}

 ?>
