<?php
//convierte a documento y descarga
function conversionYdescarga($datos, $filename) {
  header("Content-type: text/csv");                               //tipo de documento csv
  header("Content-Disposition: attachment; filename=$filename");  //asigan el nombre de archivo
  $output = fopen("php://output", "w");                           //abre
  foreach($datos as $row)  {
    fputcsv($output, array($row[0],$row[1]));                     //escribe
  }
  fclose($output);                                                //cierra la descarga
}

//recuperamos csv para la manipulación de datos
function RecuperarcsvToArray($archivo)
{
  $fila=0;                                                //contador para recuperar datos
  $archcsv=fopen($archivo,"r");
  while (($registro= fgetcsv($archcsv,1024,","))== true) {//mientras haya comas
    $num=count($registro);                                //numero de campos de cada linea
    for ($i=0; $i <$num ; $i++) {
      $datos[$fila][$i]=$registro[$i];                    //recupera cada atributo
    }
    $fila++;
  }
  fclose($archcsv);                                       //cierra el archivo
  return $datos;                                          //matriz con todos los elementos del archivo
}
?>
