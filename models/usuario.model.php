<?php
/**
 *
 */
class Usuario {
  // Variables para la tabla "usuarios"
  public $id_usuario;
  public $nombre_usuario;
  public $nickname_usuario;
  public $password_usuario;
  public $fk_puesto;
  public $estatus_usuario;
  public $resp;

  // variables para la tabla "sesiones"
  public $fk_idUsuario;
  public $ipAcceso;
  public $sistemaOperativo;
  public $fechaHoraAcceso;

}

 ?>
