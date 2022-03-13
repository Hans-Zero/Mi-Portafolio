<?php include("../template/cabecera.php");?>
<?php 


$numUsuario=$_SESSION["id"];

$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
$txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";

include("../config/bd.php");

switch($accion){

    case "Agregar":
 
        $sentenciaSQL= $conexion->prepare("INSERT INTO libros (nombre,imagen) VALUES (:nombre,:imagen);");
        $sentenciaSQL->bindParam(':nombre',$txtNombre);

        $fecha= new DateTime();
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";

        $tmpImagen=$_FILES["txtImagen"]["tmp_name"];        

        if($tmpImagen!=""){

                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

        }

        $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
         
        $sentenciaSQL->execute();

        
        
        $sentenciaSQL= $conexion->prepare("INSERT INTO coleccion (id_user,id_libro) VALUES (:user,(SELECT MAX(id) FROM libros))");
        $sentenciaSQL->bindParam(':user',$numUsuario);
        
        

        $sentenciaSQL->execute();
        
        //redirecciona al cancelar
        header("Location:productos.php");
        break;

    case "Modificar":
        $sentenciaSQL= $conexion->prepare("UPDATE libros SET nombre=:nombre WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre',$txtNombre);
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();

        if($txtImagen!=""){

            $fecha= new DateTime();
            $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";

            $tmpImagen=$_FILES["txtImagen"]["tmp_name"]; 

            //mueve el contenido de tmpImagen concatenado con nombreArchivo a la carpeta img 
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo); 

            $sentenciaSQL= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
            
            //se reemplaza la imagen antigua en la carpeta img
            if (isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg")){
                
                if(file_exists("../../img/".$libro["imagen"])){
                    
                    unlink("../../img/".$libro["imagen"]);
                }
            }
            
            //se actualiza la imagen antigua con la nueva de la base de datos
            $sentenciaSQL= $conexion->prepare("UPDATE libros SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();

        }

        //redirecciona al cancelar
        header("Location:productos.php");
        break;

    case "Cancelar":
        //redirecciona al cancelar
         header("Location:productos.php");
         break;

    case "Seleccionar":
        $sentenciaSQL= $conexion->prepare("SELECT * FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtNombre=$libro['nombre'];
        $txtImagen=$libro['imagen'];
        break; 

    case "Borrar":
        
        $sentenciaSQL= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        
        if (isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg")){
            if(file_exists("../../img/".$libro["imagen"])){
                unlink("../../img/".$libro["imagen"]);
            }
        }
        
        $sentenciaSQL= $conexion->prepare("DELETE FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();

        //redirecciona al cancelar
         header("Location:productos.php");
        break;        
    

}

// Busca en la base de datos para mostrar

$sentenciaSQL= $conexion->prepare("SELECT * FROM libros WHERE id IN (SELECT id_libro FROM coleccion WHERE id_user IN (SELECT id FROM usuarios WHERE id=:user))");
$sentenciaSQL->bindParam(':user',$numUsuario); //asigna a la variable :user el contenido de la variable numUsuario
$sentenciaSQL->execute();
$listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="col-md-5">

    <div class="card">
        <div class="card-header">
            Datos de Libros
        </div>

        <div class="card-body">
           
            <form method="POST" enctype="multipart/form-data">
                <!--required es para socilitar si o si completar el campo y readonly solo permite cargar y leer, no midificar-->
                <div class = "form-group">
                <label for="txtID">ID:</label>
                <input type="text" required readonly class="form-control" value="<?php echo $txtID;?>" name="txtID" id="txtID" placeholder="ID">
                </div>

                <div class = "form-group">
                <label for="txtNombre">Nombre:</label>
                <input type="text" required class="form-control" value="<?php echo $txtNombre;?>" name="txtNombre" id="txtNombre" placeholder="Nombre del libro">
                </div>

                <!--text-center centra tanto el texto como la imagen-->
                <div class = "form-group text-center">
                <label for="txtImagen">Imagen:</label>

                <?php echo $txtImagen;?>

                <?php 
                    if($txtImagen!=""){  ?>
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen; ?>" width="250" alt="">      
                    
                <?php } ?>
                
                
                

                <input type="file" required class="form-control" name="txtImagen" id="txtImagen" placeholder="Nombre del libro">
                </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo ($accion=="Seleccionar")?"disabled":""; ?> value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":""; ?> value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":""; ?> value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>


            </form>    


        </div>
      
    </div>   
    
    
</div>
<div class="col-md-7">
   
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listaLibros as $libro) { ?>
            <tr>
                <! se muestra el contenido de la tabla>
                <td><?php echo $libro['id'];?></td>
                <td><?php echo $libro['nombre'];?></td>
                <td>
                <!--muestra imagen en la tabla-->
                <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen']; ?>" width="50" alt="">               

                </td>

                <td>
                    <! se muestran datos de la tabla, para poder modificarlos, argegar o borrarlos>
                    <form method="post">

                    <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id']; ?>">

                    <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>

                    <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>

                    </form>
                  
                </td>

            </tr>
            <?php } ?>
            
        </tbody>
    </table>



</div>

<?php include("../template/pie.php");?>