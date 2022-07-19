<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Login. Formulario para iniciar sesión en Dashboard Collecta Global.">
    <title>LOGIN</title>
  </head>
  <body>
    <!-- <h1>Esta es la vista de Main</h1> -->
    <?php require "views/header.view.php";?>
    <section class="" id="seccion">
    <?php echo $this->alertLogin; ?>
  <div class="container py-5 h-100">
    <div class="row d-flex align-items-center justify-content-center h-100">
      <div class="col-md-8 col-lg-7 col-xl-6 h-100">
        <img src="<?php echo constant("URL"); ?>public/img/collecta.png" class="img-fluid" alt="Logotipo">
      </div>
      <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
        <form id="formLogin" action="<?php echo constant("URL"); ?>login/acceso" method="post" autocomplete="off">
          <!-- User input -->
          <div class="form-outline mb-4">
            <label class="fw-bold" for="report-user">Usuario</label>
            <input value="" type="text" name="usernameCollecta" class="form-control form-control-lg" autofocus />
          </div>
          <!-- Password input -->
          <div class="form-outline mb-4">
            <label class="fw-bold" for="report-password">Contraseña</label>
            <input value="" type="password" name="passwordCollecta" class="form-control form-control-lg" />
          </div>
          <!-- Submit button -->
          <button type="submit" class="btn btn-primary btn-lg btn-block" id="report-login">Iniciar sesión</button>
        </form>

      </div>
    </div>
  </div>
</section>
    <?php require "views/footer.view.php";?>
    <script type="text/javascript">
    $(document).ready(function() {
      $(".foot").addClass("d-none");
      if ($("#alertaLogin").length) {
        $("#alertaLogin").fadeOut(4000);
        console.log("existe");
      }
    });
    </script>
  </body>
</html>
