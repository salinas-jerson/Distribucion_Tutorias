<?php
//
function conversionYdescarga($datos, $filename = "resultados.csv", $delimiter=",") {
      $f = fopen('php://memory', 'w');
      foreach ($datos as $valor) {
          fputcsv($f, $valor, $delimiter);
      }
      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="'.$filename.'",');
      fpassthru($f);
}
//
function Distribucion_X_Docente($Distribuidos,$Matriculados,$Docentes,$AlumnosAnterior,$DatoDocentes,&$PorAsig,&$Limite) {
  //---------Numero de alumnos
  $Limite=(int)((count($Matriculados))/(count($Docentes)-1));//-1 por las filas cabecera
  $Distribucion_Docente=array();//construir nueva distribucion
  $Nuevos=array();$NoTutoria=array();//arreglos para nuevos alumnos y no tutorados
  $Nuevos=NoTutorados_O_NuevosT($Matriculados,$AlumnosAnterior);//nuevos alumnos por asignar tutor
  $NoTutoria=NoTutorados_O_NuevosT($AlumnosAnterior,$Matriculados);//Alumnos que no haran tutoria
  $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);//alumnos que sobran, atendiendo solo al limite
  //
  $fila_=0;
  for ($i=0; $i <count($Distribuidos) ; $i++) {

    if (strtolower($Distribuidos[$i][0])=="docente") {
      $Distribucion_Docente[$fila_]=$Distribuidos[$i];$i+=1;
      $cont=0;//verifica limite de alumnos
      $AuxAlumnos=array();$filAux=0;
      while (strtolower($Distribuidos[$i][0])!="docente") {
        if (Existe($Distribuidos[$i][0],$NoTutoria)){$i+=1;}//si no hace tutoria se excluye
        else {$AuxAlumnos[$filAux]=$Distribuidos[$i];$i+=1;$cont+=1;$filAux+=1;}
        if ($i ==count($Distribuidos)) {break;}
      }$i-=1;
      //
      if ($cont<$Limite) {//$Limite
        AsignarNuevosAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
        AumentarAlumno($AuxAlumnos,$Nuevos);
        $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
        $fila_=count($Distribucion_Docente);
      }
      elseif($cont==$Limite)  {
        AumentarAlumno($AuxAlumnos,$Nuevos);
        $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
        $fila_=count($Distribucion_Docente);
      }
      else {//else
        DisminuirAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
        AumentarAlumno($AuxAlumnos,$Nuevos);
        $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
        $fila_=count($Distribucion_Docente);
      }
    }
  }
  return $Distribucion_Docente;
}

// imprime los valores de datos
function Mostrar($Datos)
{
  echo "<table>";
  for ($i=0; $i < count($Datos); $i++) {
    echo "<tr>";
    foreach ($Datos[$i] as $valor) {
      echo "<td>".$valor."</td>";
    }
    echo "</tr>";
  }
  echo "</table>";
}
//aumenta 0 o 1 estudiante a un docente
function AumentarAlumno(&$AuxAlumnos,&$Nuevos)
{
  $dado = [1,1, 1, 1, 1, 1,1,1, 1,0,1,1,1,1,1,1];
  $indice_aleatorio = mt_rand(0, count($dado) - 1);
  $asignar = $dado[$indice_aleatorio];
  $fila=count($AuxAlumnos);
  if ($asignar==1) {//asigna
    if (count($Nuevos)>0) {//si hay elementos, agrega
      $AuxAlumnos[$fila]=$Nuevos[0];//agrega al final
      array_splice($Nuevos, 0, 1);//elimia el primero que a sido agregado
    }
    return ;
  }
  else {return ;}//no asigna
}

//asigna alumnos si docente tiene menos del limite
function AsignarNuevosAlumnos(&$AuxAlumnos,&$Nuevos,$Limite,$cont)
{
  $fila=count($AuxAlumnos);
  $a=$cont;
  while ($a<$Limite) {//Limite #*** <=
    if (count($Nuevos)>0) {
      $AuxAlumnos[$fila]=$Nuevos[0];$a+=1;$fila+=1;
      array_splice($Nuevos, 0, 1);//elimia el primero que a sido agregado
    }
    else {break;}
  }
  return;
}
//disminuir alumnos en caso supere el limite
function DisminuirAlumnos(&$AuxAlumnos,&$Nuevos,$Limite,$cont)
{
  $a=$cont;
  while ($a>$Limite) {//$Limite
    $b=count($Nuevos);
    $fila=count($AuxAlumnos)-1;//posicion del ultimo elemento
    $Nuevos[$b]=$AuxAlumnos[$fila];//agrega al final el alumno excdente
    array_splice($AuxAlumnos, $fila, 1);//elimia el ultimo porque tiene excedentes
    $a-=1;
  }
  return;
}

//verifica existencia de elemento en un arreglo
function Existe($elemento,$array) {
    for ($i=0; $i < count($array); $i++) {
      if ($elemento==$array[$i][0]) {return true;}
    }
    return false;//no existe
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

//no tutorados y tutorados
<<<<<<< HEAD
function NoTutorados_O_NuevosT($archivo,$archivo1){
=======
function NoT_yNuevos($archivo,$archivo1){
  $datos = array();
>>>>>>> e8eab47e1c8fd6928d8a2aaf6c1d0261e33f6387
  $fila=0;
  for ($i=0; $i < count($archivo); $i++) {
    $existe=false;
    for ($j=0; $j < count($archivo1); $j++) {
      if ($archivo[$i][0]==$archivo1[$j][0]) { $existe=true; break;   }// existe alumno en las 2 tablas
    }
    if (!$existe) {//si no existe guardamos (no hace tutoría o es nuevo)
      $datos[$fila]=$archivo[$i];
      $fila+=1;
    }
  }
  return $datos;//matriz con no tutorados o tutorados
}
#alumnos matriculados el anterior semestre, a partir de la distribución de alumnosxDocente
function MatriculadosAnterior($archivo){
  
  $fila=0;//contador para recuperar datos
  $fila1=0;//contador para recuperar docentes
  for ($k=0; $k < count($archivo); $k++) {//mientras haya comas
    $str=strtolower($archivo[$k][0]);
    if ($str!="docente") {//es alumno
      $datos[$fila]=$archivo[$k];
      $fila++;
    }
    else{//es docente
      $datosD[$fila1]=$archivo[$k];
      $fila1++;
    }
  }
  //en datos esta solos alumnos y en datosD solo docentes
  return array($datos,$datosD);//matriz con todos alimnos y docentes
}
?>
