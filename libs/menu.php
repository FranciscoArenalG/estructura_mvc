<?php
/**
 *
 */
class Menu{
  // Variables de la tabla "cat_menu"
  private $idMenu;
  private $nombreMenu;
  private $descripcionMenu;
  private $referenciaMenu;
  private $iconoMenu;
  private $posicionMenu;
  private $estatusMenu;

  //Variables de la tabla "cat_submenu"
  private $idSubmenu;
  private $fkMenu;
  private $nombreSubmenu;
  private $descripcionSubmenu;
  private $referenciaSubmenu;
  private $estatusSubmenu;

  function __construct(){
  }

  public function getMenu($estructura){
    $con = new Database();
    try {
      $query = $con->connect()->prepare("SELECT * FROM cat_menu WHERE nombre_menu != 'Iniciar sesión' ORDER BY posicion_menu ASC");
      $query->execute();
      $items = $query->fetchAll();
      return $items;
    } catch (PDOException $e) {
      echo "error: " . $e->getMessage();
      return [];
    }
  }

  public function getMenuLogin(){
    $con = new Database();
    try {
      $query = $con->connect()->query("SELECT * FROM cat_menu WHERE nombre_menu IN('Iniciar sesión')");
      $items = $query->fetchAll();
      return $items;
    } catch (PDOException $e) {
      echo "error: " . $e->getMessage();
      return [];
    }
  }

  public function getByIdMenuSubmenu($id){
    $con = new Database();
    try {
      $query = $con->connect()->prepare("SELECT * FROM cat_submenu WHERE fk_id_menu = :fkMenu");
      $query->execute(['fkMenu'=>$id]);
      return $query->fetchAll();
    } catch (PDOException $e) {
      echo "error: " . $e->getMessage();
      return false;
    }
  }
  //
  // public function getSubMenu($id){
  //   try {
  //     $query = $this->db->connect()->prepare("SELECT * FROM submenu WHERE matricula = :matricula");
  //     $query->execute(['matricula'=>$id]);
  //     while ($row=$query->fetch()) {
  //       $item->matricula = $row['matricula'];
  //       $item->nombre = $row['nombre'];
  //       $item->apellido = $row['apellido'];
  //     }
  //   } catch (PDOException $e) {
  //
  //   }
  //
  // }

}

 ?>
