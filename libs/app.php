<?php
session_start();
// $_SESSION['id_usuario'] = 1;
// session_destroy();
require_once "controllers/error.controller.php";
/**
 * Francisco Arenal Guerrero
 */
class App
{
    private $url;
    public function __construct()
    {
        // echo "<p>Nueva App</p>";

        $this->url = isset($_GET['url']) ? $_GET['url'] : null;
        $this->url = rtrim($this->url, "/");
        $this->url = explode("/", $this->url);
        // Cuando se ingresa sin definir el controlador
        if (isset($_SESSION['id_usuario-' . constant('Sistema')]) && !empty($_SESSION['id_usuario-' . constant('Sistema')])) {
            if (empty($this->url[0])) {
                $controller = "";
                $archivoController = "";
                switch ($_SESSION['fk_estructura-' . constant('Sistema')]) {
                    case '1':
                        $archivoController = "controllers/dashboard.controller.php";
                        require_once $archivoController;
                        $controller = new Dashboard();
                        $controller->loadModel("dashboard");
                        $controller->render();
                        break;
                    case '2':
                      $archivoController = "controllers/dashboardd.controller.php";
                      require_once $archivoController;
                      $controller = new Dashboardd();
                      $controller->loadModel("dashboardd");
                      $controller->render();
                      break;
                    case '3':
                        $archivoController = "controllers/dashboardt.controller.php";
                        require_once $archivoController;
                        $controller = new Dashboardt();
                        $controller->loadModel("dashboardt");
                        $controller->render();
                        break;

                    default:
                        $controller = new Errores();
                        break;
                }
                return false;
            }
            $archivoController = "controllers/" . $this->url[0] . ".controller.php";

            if (file_exists($archivoController)) {
                require_once $archivoController;
                // Inicializa el controlador
                $controller = new $this->url[0];
                $controller->loadModel($this->url[0]);
                // Número de elementos del arreglo URL
                $nparam = sizeof($this->url);
                if ($nparam > 1) {
                    if ($nparam > 2) {
                        $param = [];
                        for ($i = 2; $i < $nparam; $i++) {
                            array_push($param, $this->url[$i]);
                        }
                        if (method_exists($controller, $this->url[1])) {
                            $controller->{$this->url[1]}($param);
                        } else {
                            $controller = new Errores();
                        }
                        /* var_dump($param) ; */
                        // $controller->{$this->url[1]}($param);
                    } else {
                        if (method_exists($controller, $this->url[1])) {
                            // echo "existe metodo";
                            $controller->{$this->url[1]}(); //Carga el metodo
                        } else {
                            // echo "no existe metodo";
                            $controller = new Errores();
                        }
                        // $controller->{$this->url[1]}();//Carga el metodo
                    }
                } else {
                    $controller->render();
                }
            } else {
                $controller = new Errores();
            }
        } else {
            // echo "No esta logueado, redireccionar a Login";
            $this->parametrosLogin();
        }

    }
    public function parametrosLogin()
    {
        if (empty($this->url[0])) {
            $archivoController = "controllers/login.controller.php";
            require_once $archivoController;
            $controller = new Login();
            $controller->loadModel("login");
            $controller->render();

            return false;
        } else {
            $archivoController = "controllers/" . $this->url[0] . ".controller.php";

            if (file_exists($archivoController)) {
                require_once $archivoController;
                // Inicializa el controlador
                $controller = new $this->url[0];
                $controller->loadModel($this->url[0]);
                // Número de elementos del arreglo URL
                $nparam = sizeof($this->url);
                if ($nparam > 1) {
                    if ($nparam > 2) {
                        $param = [];
                        for ($i = 2; $i < $nparam; $i++) {
                            array_push($param, $this->url[$i]);
                        }
                        if (method_exists($controller, $this->url[1])) {
                            $controller->{$this->url[1]}($param);
                        } else {
                            $controller = new Errores();
                        }
                    } else {
                        if (method_exists($controller, $this->url[1])) {
                            // echo "existe metodo";
                            $controller->{$this->url[1]}(); //Carga el metodo
                        } else {
                            // echo "no existe metodo";
                            $controller = new Errores();
                        }
                    }
                } else {
                    $controller->render();
                }
            } else {
                $controller = new Errores();
            }
        }

    }
}
