<?php
//convierte a documento y descarga
function conversionYdescarga($datos, $filename) {
  //
  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename=$filename");
  $output = fopen("php://output", "w");
  foreach($datos as $row)  {
    fputcsv($output, array($row[0],$row[1]));
  }
  fclose($output);
}

//recuperamos csv para la manipulación de datos
function RecuperarcsvToArray($archivo)
{
  $fila=0;//contador para recuperar datos
  $archcsv=fopen($archivo,"r");
  while (($registro= fgetcsv($archcsv,1024,","))== true) {//mientras haya comas
    $num=count($registro);//numero de campos de cada linea
    for ($i=0; $i <$num ; $i++) {
      $datos[$fila][$i]=$registro[$i];//recupera cada atributo
    }
    $fila++;
  }
  fclose($archcsv);
  return $datos;//matriz con todos los elementos del archivo
}
?>
