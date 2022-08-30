<?php
include("distribucionAlumno.php");
include("gestorArchivo.php");
$BotonB="";
$BotonDescargar="";
$dir ="files/";
$permitico=array('csv');

$distribuidos=$_FILES["distribuidos"]["name"];
$matriculados=$_FILES["matriculados"]["name"];
$docentes=$_FILES["docentes"]["name"];
$Option=$_POST['eleccioN'];
if (isset($_POST['Bbuscar'])){
  $BotonB=$_POST['Bbuscar'];
}
if (isset($_POST['Bdescarga'])){
  $BotonDescargar=$_POST['Bdescarga'];
}

$ruta1=$dir.$distribuidos;
$ruta2=$dir.$matriculados;
$ruta3=$dir.$docentes;
$extencion1=explode(".",$distribuidos);
$extencion2=explode(".",$matriculados);
$extencion3=explode(".",$docentes);
$ex1=strtolower(end($extencion1));
$ex2=strtolower(end($extencion2));
$ex3=strtolower(end($extencion3));

//crea directorio si no existe
if (!file_exists($dir)) {
  mkdir($dir,0777);
}

if (in_array($ex1,$permitico)) {
  if (in_array($ex2,$permitico)) {
    if (in_array($ex3,$permitico)) {
      move_uploaded_file($_FILES["distribuidos"]["tmp_name"],$ruta1);
      move_uploaded_file($_FILES["matriculados"]["tmp_name"],$ruta2);
      move_uploaded_file($_FILES["docentes"]["tmp_name"],$ruta3);

      //recuperando archivos csv a arraglos BiDimencional
      $Matriculados=array();$Docentes=array();$Distribuidos=array();
      $Matriculados=RecuperarcsvToArray($dir.$matriculados);//recupera los matriculados en el presente semestre
      array_splice($Matriculados, 0, 1);//elimina cabecera
      $Distribuidos=RecuperarcsvToArray($dir.$distribuidos);//recupera alumnos distribuidos del semestre anterior
      $Docentes=RecuperarcsvToArray($dir.$docentes);//recupera los docentes del presente semestre

      if ($BotonB) {
        $AlumnosAnterior=array();$DatoDocentes=array();$GetAlumnos_Docentes=array();
        $GetAlumnos_Docentes=MatriculadosAnterior($Distribuidos);
        $AlumnosAnterior=$GetAlumnos_Docentes[0];//solo alumnos del anterior semestre
        $DatoDocentes=$GetAlumnos_Docentes[1];//solo docentes del anterior semestre

        if ($Option=="distribucion") {
          $Limite=$Limite=(int)((count($Matriculados))/(count($Docentes)-1));//-1 por las filas cabecera
          $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);//alumnos que sobran, atendiendo solo al limite

          $Distribucion_Docente=array();$NoTutorados=array();$NuevosT=array();
          $NuevosT=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);
          $NoTutorados=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);
          $Distribucion_Docente=Distribucion_X_Docente($Distribuidos,$NuevosT,$NoTutorados,$PorAsig,$Limite);
          //
          printf(" Distribución de alumnos por docente <br>");
          printf($PorAsig." Docentes tendran mas 1 alumno <br>");
          printf("N° Matriculados ".(count($Matriculados)).", N° DocenteS ".(count($Docentes)-1).",  Asignacion a ".$Limite." alumnos"."<br><br>");

          //echo .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          Mostrar($Distribucion_Docente);
        }
        elseif ($Option=="noTutorados") {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);// en datos está todo un distribuidos
          //
          echo "Alumnos no tutorados :-) <br><br>";
          //echo .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          Mostrar($Datos);//mostar alumnos no turorados
        }
        else {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);// en datos está todo un distribuidos
          //
          echo "Alumnos tutorados :)<br> <br>";
          //echo .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          Mostrar($Datos);//alumnos tutorados
        }
      }
      //otro boton
      elseif ($BotonDescargar) {
        $AlumnosAnterior=array();$DatoDocentes=array();$GetAlumnos_Docentes=array();
        $GetAlumnos_Docentes=MatriculadosAnterior($Distribuidos);
        $AlumnosAnterior=$GetAlumnos_Docentes[0];//solo alumnos del anterior semestre
        $DatoDocentes=$GetAlumnos_Docentes[1];//solo docentes del anterior semestre

        if ($Option=="distribucion") {
          //
          $Limite=$Limite=(int)((count($Matriculados))/(count($Docentes)-1));//-1 por las filas cabecera
          $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);//alumnos que sobran, atendiendo solo al limite

          $Distribucion_Docente=array();$NoTutorados=array();$NuevosT=array();
          $NuevosT=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);
          $NoTutorados=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);
          $Distribucion_Docente=Distribucion_X_Docente($Distribuidos,$NuevosT,$NoTutorados,$PorAsig,$Limite);
          //descargar co el nombre de filename
          $filename = 'NuevaDistribucion.csv';
          conversionYdescarga($Distribucion_Docente,$filename);
        }
        elseif ($Option=="noTutorados") {
          $DatosNo=array();
          $DatosNo=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);// en datos está todo un distribuidos
          //descargar co el nombre de filename
          $filename = 'NoTutorados.csv';
          conversionYdescarga($DatosNo,$filename);
        }
        else {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);// en datos está todo un distribuidos
          //descargar co el nombre de filename
          $filename = 'Tutorados.csv';
          conversionYdescarga($Datos,$filename);
        }
      }

    }else {echo "para este propósito3, solo se permite archivo de extencion .csv. Vuelva a cargar";}
  }else {echo "para este propósito2, solo se permite archivo de extencion .csv. Vuelva a cargar";}
}else {echo "para este propósito1, solo se permite archivo de extencion .csv. Vuelva a cargar";}
?>
