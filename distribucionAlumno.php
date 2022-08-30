<?php
//distribuye alumno spor docente
function Distribucion_X_Docente($Distribuidos,$Nuevos,$NoTutoria,&$PorAsig,&$Limite) {
  $Distribucion_Docente=array();//construir nueva distribucion
  //
  $fila_=0;
  for ($i=0; $i <count($Distribuidos) ; $i++) {
    if (strtolower($Distribuidos[$i][0])=="docente") {//es docente
      $Distribucion_Docente[$fila_]=$Distribuidos[$i];$i+=1;
      $cont=0;//verifica limite de alumnos
      $AuxAlumnos=array();$filAux=0;
      while (strtolower($Distribuidos[$i][0])!="docente") {//es alumnos
        $existe=false;
        for ($j=0; $j < count($NoTutoria); $j++) {
          if ($Distribuidos[$i][0]==$NoTutoria[$j][0]) {
            $existe = true;
          }
        }
        if ($existe){$i+=1;}//si no hace tutoria se excluye
        else {$AuxAlumnos[$filAux]=$Distribuidos[$i];$i+=1;$cont+=1;$filAux+=1;}//hace tutoria
        if ($i ==count($Distribuidos)) {break;}
      }$i-=1;
      //balance alumnos
      Balancear($Distribucion_Docente, $AuxAlumnos,$Nuevos,$Limite,$cont,$fila_);
    }
  }
  return $Distribucion_Docente;
}

//balance de alumnos
function Balancear(&$Distribucion_Docente, &$AuxAlumnos,&$Nuevos,$Limite,$cont,&$fila_)
{
  if ($cont<$Limite) {//tiene menos del limite
    $a=$cont;
    while ($a<$Limite) {//
      if (AumentarAlumno($AuxAlumnos,$Nuevos)) {
        $a+=1;
      }
      elseif (count($Nuevos)<=0) {
          break;
      }
    }
    AumentarAlumno($AuxAlumnos,$Nuevos);
    $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
    $fila_=count($Distribucion_Docente);
  }
  elseif($cont==$Limite)  {//igual al limite
    AumentarAlumno($AuxAlumnos,$Nuevos);
    $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
    $fila_=count($Distribucion_Docente);
  }
  else {//supera el límite de alumnos
    DisminuirAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
    AumentarAlumno($AuxAlumnos,$Nuevos);
    $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
    $fila_=count($Distribucion_Docente);
  }
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
  if ($asignar==1) {
    if (count($Nuevos)>0) {//si hay elementos, agrega
      $AuxAlumnos[$fila]=$Nuevos[0];//agrega al final
      array_splice($Nuevos, 0, 1);//elimia el primero que a sido agregado
    }
    return true;
  }
  else {return false;}
}

//disminuir alumnos en caso supere el limite
function DisminuirAlumnos(&$AuxAlumnos,&$Nuevos,$Limite,$cont)
{
  $a=$cont;
  while ($a>$Limite) {//$Limite
    $b=count($Nuevos);
    $fila=count($AuxAlumnos)-1;//posicion del ultimo elemento
    $Nuevos[$b]=$AuxAlumnos[$fila];//agrega al final el alumno excdente
    array_splice($AuxAlumnos, $fila, 1);//elimia el ultimo porque excedentes
    $a-=1;
  }
  return;
}

//no tutorados o tutorados
function NoTutorados_O_NuevosTut($archivo,$archivo1){
  $datos = array();
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
  return $datos;//matriz con notutorados o nuevos
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
  return array($datos,$datosD);//matriz con todos los alumnos, docentes
}
?>
