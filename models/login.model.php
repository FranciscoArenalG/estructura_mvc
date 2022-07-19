<?php
/**
 *
 */
include_once "models/usuario.model.php";
class LoginModel extends ModelBase
{

    public function __construct()
    {
        parent::__construct();
    }

    // public function get(){
    //   $items = [];
    //   try {
    //     $query = $this->db->connect()->query("SELECT * FROM alumnos");
    //     while ($row = $query->fetch()) {
    //       $item = new Alumno();
    //       $item->matricula = $row['matricula'];
    //       $item->nombre = $row['nombre'];
    //       $item->apellido = $row['apellido'];
    //
    //       array_push($items, $item);
    //     }
    //     return $items;
    //   } catch (PDOException $e) {
    //     return [];
    //   }
    //
    // }

    public function getFindByUsuario($nickname)
    {
        $usuario = new Usuario();
        try {
            $query = $this->db->connect()->prepare("SELECT * FROM tb_c_usuario_dashboard_dos WHERE nickname_usuario = :nickname");
            $query->execute(['nickname' => $nickname]);
            try {
                while ($row = $query->fetch()) {
                    // Variables de la tabla "usuarios"
                    $usuario->id_usuario = $row['id_usuario'];
                    $usuario->nombre_usuario = $row['nombre_usuario'];
                    $usuario->correo_usuario = $row['correo_usuario'];
                    $usuario->nickname_usuario = $row['nickname_usuario'];
                    $usuario->password_usuario = $row['password_usuario'];
                    $usuario->fk_estructura = $row['fk_estructura'];
                    $usuario->estatus_usuario = $row['estatus_usuario'];
                    /* $usuario->estatusUsuario = $row['estatusUsuario']; */
                    $usuario->resp = true;
                    // return true;
                }
                return $usuario;
            } catch (\Exception $e) {
              echo $e->getMessage();
                return false;
                return [];
            }

        } catch (PDOException $e) {
            return [];
        }
    }

    public function getCliente($usuario){
        try {
            $query = $this->db->connect()->prepare("SELECT fk_id_c_cliente FROM tb_c_cliente_usuario_dashboard WHERE fk_usuario_dashboard = :usuario");
            $query->execute(['usuario' => $usuario]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "error: ". $e->getMessage();
            //return [];
        }
    }

    public function insertSesion()
    {
        try {
            $cn = new PDO("pgsql:host=34.130.137.64;port=5432;dbname=collectacloudold", "postgres", "C0ll3ct42021!");
        } catch (PDOException $e) {
            print_r("Error de conexión: " . $e->getMessage());
            return false;
        }
        $usuario = new Usuario();
        $query = $cn->prepare("INSERT INTO tb_sesiones (fk_idusuario, ip_acceso, sistema_operativo) VALUES (:idUsuario, :ipAcceso, :sistemaOperativo)");
        try {
            $query->execute([
                'idUsuario' => $_SESSION['id_usuario-' . constant('Sistema')],
                'ipAcceso' => $this->getRealIP(),
                'sistemaOperativo' => $this->getPlatform()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "error recuperado: " . $e;
            return false;
        }

    }
    public function update($item)
    {
        $query = $this->db->connect()->prepare("UPDATE alumnos SET nombre = :nombre, apellido = :apellido WHERE matricula = :matricula");
        try {
            $query->execute([
                'matricula' => $item['matricula'],
                'nombre' => $item['nombre'],
                'apellido' => $item['apellido'],
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }

    }

    public function delete($id)
    {
        $query = $this->db->connect()->prepare("DELETE FROM alumnos WHERE matricula = :id");
        try {
            $query->execute([
                'id' => $id,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getPlatform()
    {
        $plataformas = array(
            'Windows 10' => 'Windows NT 10.0+',
            'Windows 8.1' => 'Windows NT 6.3+',
            'Windows 8' => 'Windows NT 6.2+',
            'Windows 7' => 'Windows NT 6.1+',
            'Windows Vista' => 'Windows NT 6.0+',
            'Windows XP' => 'Windows NT 5.1+',
            'Windows 2003' => 'Windows NT 5.2+',
            'Windows' => 'Windows otros',
            'iPhone' => 'iPhone',
            'iPad' => 'iPad',
            'Mac OS X' => '(Mac OS X+)|(CFNetwork+)',
            'Mac otros' => 'Macintosh',
            'Android' => 'Android',
            'BlackBerry' => 'BlackBerry',
            'Linux' => 'Linux',
        );
        foreach ($plataformas as $plataforma => $pattern) {
            if (preg_match('/(?i)' . $pattern . '/', $_SERVER['HTTP_USER_AGENT'])) {
                return $plataforma;
            }

        }
        return 'Otras';
    }

    public function getRealIP()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }

    }

}
