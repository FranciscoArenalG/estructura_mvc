<?php
/**
 *
 */
require_once("libs/encrypt_decrypt.php");
class Login extends ControllerBase{

  function __construct(){
    parent::__construct();
    $this->view->alertLogin = "";
    $this->view->usuarios = [];
    if (isset($_SESSION['idUsuario-'.constant('Sistema')])) {
      header("location:".constant('URL'));
    }
  }

  function render(){
    $this->view->render('login/index');
  }

  function acceso(){
    if (!isset($_POST['usernameCollecta']) || !isset($_POST['passwordCollecta'])) {
      header("location:".constant('URL')."login");
    }else {
      $user = $_POST['usernameCollecta'];$pass =encrypt_decrypt('encrypt', $_POST['passwordCollecta']);
      if (!empty($user) && !empty($pass)) {
        $usuario = $this->model->getFindByUsuario($user);
        if ($usuario->resp) {
          // echo "Existe usuario";
          if ($usuario->password_usuario == $pass) {
            // echo "Contraseña correcta";
            if ($usuario->estatus_usuario == 1) {
              // $_SESSION['idUsuario-'.constant('Sistema')] = 1;
              $_SESSION['id_usuario-'.constant('Sistema')] = $usuario->id_usuario;
              $_SESSION['nombre_usuario-'.constant('Sistema')] = $usuario->nombre_usuario;
              $_SESSION['nickname_usuario-'.constant('Sistema')] = $usuario->nickname_usuario;
              $_SESSION['password_usuario-'.constant('Sistema')] = $usuario->password_usuario;
              $_SESSION['fk_puesto-'.constant('Sistema')] = $usuario->fk_puesto;
              $_SESSION['estatus_usuario-'.constant('Sistema')] = $usuario->estatus_usuario;
              // // echo "Usuario y constraseña recibidos";
              $this->model->insertSesion();
              header("location:".constant('URL'));
            }else {
              $mensaje = '<br><div id="alertaLogin" class="alert alert-warning text-center h5" role="alert">Su cuenta se encuentra inactiva!</div>';
              $this->view->alertLogin = $mensaje;
              $this->render();
            }
          }else {
            $mensaje = '<br><div id="alertaLogin" class="alert alert-danger text-center h5" role="alert">Contraseña incorrecta!</div>';
            $this->view->alertLogin = $mensaje;
            $this->render();
          }
        }else {
          $mensaje = '<br><div id="alertaLogin" class="alert alert-danger text-center h5" role="alert">El usuario ingresado no existe!</div>';
          $this->view->alertLogin = $mensaje;
          $this->render();
        }

      }else {
        $mensaje = '<br><div id="alertaLogin" class="alert alert-danger text-center h5" role="alert">Falta capturar usuario o contaseña!</div>';
        $this->view->alertLogin = $mensaje;
        $this->render();
      }
    }
  }

  function salir(){
    unset($_SESSION['id_usuario-'.constant('Sistema')]);
    unset($_SESSION['nombre_usuario-'.constant('Sistema')]);
    unset($_SESSION['correo_usuario-'.constant('Sistema')]);
    unset($_SESSION['nickname_usuario-'.constant('Sistema')]);
    unset($_SESSION['password_usuario-'.constant('Sistema')]);
    unset($_SESSION['fk_id_c_cliente-'.constant('Sistema')]);
    unset($_SESSION['estatus_usuario-'.constant('Sistema')]);
    // session_destroy();
    header("location:".constant('URL'));
  }
}

 ?>
