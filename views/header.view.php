<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="SIE, Sistema de Inventario de expedientes">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo constant("URL");?>public/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo constant("URL");?>public/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo constant("URL");?>public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo constant("URL");?>public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  </head>
  <body>
    <?php $menu = new Menu();?>
    <?php if (isset($_SESSION['id_usuario-'.constant('Sistema')]) && !empty($_SESSION['id_usuario-'.constant('Sistema')])): ?>
    <nav class="border-bottom navbar navbar-expand-md navbar-light navbar-white bg-white sticky-top">
      <div class="container">
      <a class="navbar-brand" href="#">
        <img src="<?=constant("URL")?>public/img/logo_lahe.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8;margin-top: -0.5rem;margin-right: 0.2rem;height: 33px;">
        <span class="brand-text font-weight-light"><?php echo $_SESSION['nombre_usuario-'.constant('Sistema')]; ?></span>
      </a>
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          <?php foreach ($menu->getMenu($_SESSION['fk_puesto-'.constant('Sistema')]) as $item): ?>
            <?php if ($menu->getByIdMenuSubmenu($item['id_menu']) == false): ?>
                    <li class="nav-item"><a class="nav-link redireccionMenu" href="<?=constant("URL").$item['referencia_menu'];?>" title="<?=$item['descripcion_menu'];?>"><?=$item['nombre_menu'];?></a></li>
            <?php else: ?>
              <li class="nav-item dropdown" title="<?=$item['descripcion_menu'] ?>">
              <a class="nav-link dropdown-toggle" href="#<?=$item['nombre_menu'];?>" id="menu-<?=$item['nombre_menu'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?=$item['nombre_menu'];?>
              </a>
              <div class="dropdown-menu" aria-labelledby="menu-<?=$item['nombre_menu'];?>">
                <?php foreach ($menu->getByIdMenuSubmenu($item['id_menu']) as $submenu): ?>
                  <a class="dropdown-item" href="<?=constant("URL").$submenu['referencia_submenu'];?>" title="<?=$submenu['descripcion_submenu'];?>"><?=$submenu['nombre_submenu'];?></a>
                <?php endforeach; ?>
              </div>
            </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
        <!-- <span class="navbar-text">
          <?php echo $_SESSION['nombre_usuario-'.constant('Sistema')]; ?>
        </span> -->
      </div>
      
      </div>
    </nav>
    <?php else: ?>
    <nav class="border-bottom navbar navbar-expand-lg navbar-light navbar-white bg-white sticky-top">
      <div class="container">
      <a class="navbar-brand" href="#">
        <img src="<?=constant("URL")?>public/img/logo_lahe.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8;margin-top: -0.5rem;margin-right: 0.2rem;height: 33px;">
        <span class="brand-text font-weight-light">Dashboard</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav ml-auto">
          <?php foreach ($menu->getMenuLogin() as $item): ?>
              <li class="nav-item"><a class="nav-link" href="<?=constant("URL").$item['referencia_menu'];?>" title="<?=$item['descripcion_menu'];?>"><?=$item['nombre_menu'];?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      </div>
    </nav>
    <?php endif; ?>
    <div class="container mt-2">
  </body>
</html>
