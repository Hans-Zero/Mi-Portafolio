<?php include("template/cabecera login.php"); ?>
<?php


$usuario=(isset($_POST['usuario']))?$_POST['usuario']:"";
$contrasenia=(isset($_POST['contrasenia']))?$_POST['contrasenia']:"";

session_start();
$_SESSION['usuario']=$usuario;

$conexion=mysqli_connect("localhost","root","","sitio");

$consulta="SELECT * FROM usuarios where usuario='$usuario' and contrasenia='$contrasenia'";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_fetch_array($resultado);

//se pregunta si hay envio
if($_POST){
    if($filas['usuario']==$usuario){ //administrador

        $_SESSION['usuario']="ok";
        $_SESSION['nombreUsuario']=$filas['usuario'];
        $_SESSION['id']=$filas['id'];
        header('Location:inicio.php');
    }else{
        $mensaje="Error: El usuario o contraseña son incorrectos";
    }
    
}

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Administrador del sitio web</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
        <div class="container">
            <div class="row">

                <div class="col-md-4">
                    
                </div>               

                <div class="col-md-4">
                    <br/><br/><br/>
                        
                    <div class="card">
                        <div class="card-header">
                            Login
                        </div>
                        <div class="card-body">

                        <?php if(isset($mensaje)) {?>

                            <div class="alert alert-danger" role="alert">
                                <?php echo $mensaje;?>
                            </div>
                            
                        <?php } ?>
                            <!--Formulario para ingresar los datos de logueo-->
                            <form method="POST">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Usuario</label>
                                    <input type="text" class="form-control" name="usuario" placeholder="Escribe tu usuario">          
                                    
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputPassword1">Contraseña:</label>
                                    <input type="password" class="form-control" name="contrasenia" placeholder="Escribe tu contraseña">                            
                                    
                                </div>
                                <button type="submit" class="btn btn-primary">Entrar al administrador</button>


                            </form>

                        </div>
                        
                        </div>
                    </div>


                </div>
                
            </div>
        </div> 

<?php include("template/pie.php"); ?>




