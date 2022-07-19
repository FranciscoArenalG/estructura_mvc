<?php
/**
 *
 */
class ControllerBase{

  function __construct(){
    // echo "<p>Controlador base</p>";
    $this->view = new ViewBase();

  }

  function loadModel($model){
    $url = "models/".$model.".model.php";

    if (file_exists($url)) {
      require $url;

      $modelName = $model."Model";
      $this->model = new $modelName;
    }
  }
}

 ?>
