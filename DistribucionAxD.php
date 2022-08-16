<?php
include("alumno.php");
function DistibuirAxD($archivo,$archivo1){
  $fila=0;//contador para recuperar datos
  $archcsv=fopen($archivo,"r");
  while (($registro= fgetcsv($archcsv,1024,","))== true) {//mientras haya comas
    $archcsv1=fopen($archivo1,"r");
    $existe=false;
    while (($registro1= fgetcsv($archcsv1,1024,","))== true){//mientras haya elementos
      if ($registro[0]==$registro1[0]) { $existe=true; break;   }// existe alumno en las 2 tablas
    }
    if (!$existe) {//si no existe guardamos (no hace tutoría o es nuevo)
      $num=count($registro);//numero de campos de cada linea
      for ($i=0; $i <$num ; $i++) {
        $datos[$fila][$i]=$registro[$i];//recupera cada atributo
      }
      $fila++;
    }
    fclose($archcsv1);
  }
  fclose($archcsv);
  return $datos;//matriz con todos los elementos del archivo
}
?>
