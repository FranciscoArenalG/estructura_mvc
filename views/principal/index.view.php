<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?=$this->icono;?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
</head>
<body>
<?php require "views/header.view.php";?>
    <div class="card">
    <div class="card-header">
        Página Principal
    </div>
    <div class="card-body">
        <h5 class="card-title">Bienvenido</h5>
        <p class="card-text">Está es la página principal después del login.</p>
    </div>
    </div>
<?php require "views/footer.view.php";?>
</body>
</html>