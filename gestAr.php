<?php
include("alumno.php");

$dir ="files/";
$permitico=array('csv');

$archivo1=$_FILES["archivo1"]["name"];
$archivo2=$_FILES["archivo2"]["name"];
$Option=$_POST['eleccioN'];
$BotonB=$_POST['Bbuscar'];
$tabla=$_POST["textarea"];

$ruta1=$dir.$archivo1;
$ruta2=$dir.$archivo2;
$extencion1=explode(".",$archivo1);
$extencion2=explode(".",$archivo2);
$ex1=strtolower(end($extencion1));
$ex2=strtolower(end($extencion2));


if (!file_exists($dir)) {
  mkdir($dir,0777);
}

if (in_array($ex1,$permitico)) {
  if (in_array($ex2,$permitico)) {
    move_uploaded_file($_FILES["archivo1"]["tmp_name"],$ruta1);
    move_uploaded_file($_FILES["archivo2"]["tmp_name"],$ruta2);

    if ($BotonB) {
      if ($Option=="noTutorados") {
        $Datos=array();
        $Datos=listarAlumnos($dir.$archivo1,$dir.$archivo2);// en datos está todo un archivo1
        echo "<table>";
        //echo .... cabeceras de la tabla
        for ($i=0; $i < count($Datos); $i++) {
          echo "<tr>";
          foreach ($Datos[$i] as $valor) {
            echo "<td>".$valor."</td";
          }
          echo "</tr>";
        }
        echo "</table>";
      } else {
        $Datos=array();
        $Datos=listarAlumnos($dir.$archivo2,$dir.$archivo1);// en datos está todo un archivo1
        echo "<table>";
        //echo .... cabeceras de la tabla
        for ($i=0; $i < count($Datos); $i++) {
          //echo "<tr>";
          foreach ($Datos[$i] as $valor) {
            //echo "<td>".$valor."</td>";

          }
          //$tabla
        //  echo "</tr>";
        }
        $tabla=$Datos;
        echo "</table>";
      }
    }
  }
  else {
    echo "para este propósito2, solo se permite archivo de extencion .csv";
  }
}
else {
  echo "para este propósito1, solo se permite archivo de extencion .csv";
}


?>
