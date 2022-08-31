<?php
//archivos de modulos
include("distribucionAlumno.php");
include("gestorArchivo.php");
$BotonB="";
$BotonDescargar="";
$dir ="files/";
$permitico=array('csv');                        //archivos permitidos

$distribuidos=$_FILES["distribuidos"]["name"];  //recive formulario
$matriculados=$_FILES["matriculados"]["name"];
$docentes=$_FILES["docentes"]["name"];
$Option=$_POST['eleccioN'];
if (isset($_POST['Bbuscar'])){                  //
  $BotonB=$_POST['Bbuscar'];
}
if (isset($_POST['Bdescarga'])){
  $BotonDescargar=$_POST['Bdescarga'];
}
//preparación de rutas para guardar los archivos
$ruta1=$dir.$distribuidos;
$ruta2=$dir.$matriculados;
$ruta3=$dir.$docentes;
//valida archivos
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

if (in_array($ex1,$permitico)) {      //verifica si son permitidos los archivos
  if (in_array($ex2,$permitico)) {
    if (in_array($ex3,$permitico)) {                                  //mueve los archivos
      move_uploaded_file($_FILES["distribuidos"]["tmp_name"],$ruta1);
      move_uploaded_file($_FILES["matriculados"]["tmp_name"],$ruta2);
      move_uploaded_file($_FILES["docentes"]["tmp_name"],$ruta3);

      $Matriculados=array();$Docentes=array();$Distribuidos=array(); // arraglos BiDimencional para el recuperado de archivos csv
      $Matriculados=RecuperarcsvToArray($dir.$matriculados);         //recupera los matriculados en el presente semestre
      array_splice($Matriculados, 0, 1);                             //elimina cabecera
      $Distribuidos=RecuperarcsvToArray($dir.$distribuidos);         //recupera alumnos distribuidos del semestre anterior
      $Docentes=RecuperarcsvToArray($dir.$docentes);                 //recupera los docentes del presente semestre

      if ($BotonB) {
        $AlumnosAnterior=array();$DatoDocentes=array();$GetAlumnos_Docentes=array();
        $GetAlumnos_Docentes=MatriculadosAnterior($Distribuidos);
        $AlumnosAnterior=$GetAlumnos_Docentes[0];                             //solo alumnos del anterior semestre
        $DatoDocentes=$GetAlumnos_Docentes[1];                                //solo docentes del anterior semestre

        if ($Option=="distribucion") {
          $Limite=$Limite=(int)((count($Matriculados))/(count($Docentes)-1)); //limite de alumnos por docente, -1 por las filas cabecera
          $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);       //alumnos que sobran,quedan por asignar, atendiendo solo al limite

          $Distribucion_Docente=array();$NoTutorados=array();$NuevosT=array();
          $NuevosT=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);
          $NoTutorados=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);
          $Distribucion_Docente=Distribucion_X_Docente($Distribuidos,$NuevosT,$NoTutorados,$PorAsig,$Limite);
          //
          printf(" <h1> Distribución de alumnos por docente </h1> ");
          printf("<h3>".$PorAsig." Docentes tendran mas 1 alumno </h3> ");
          printf("<h3>N° Matriculados ".(count($Matriculados)).", N° DocenteS ".(count($Docentes)-1).",  Asignacion a ".$Limite." alumnos </h3>");
          // .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          Mostrar($Distribucion_Docente);
        }
        elseif ($Option=="noTutorados") {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);// arreglo de no tutorados
          //
          echo "<h1>Alumnos no tutorados </h1> ";                       // .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          Mostrar($Datos);//mostar alumnos no turorados
        }
        else {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);// arreglo de tutorados
          //
          echo "<h1>Alumnos tutorados </h1> ";                          // .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          Mostrar($Datos);//alumnos tutorados
        }
      }
      //otro boton
      elseif ($BotonDescargar) {
        $AlumnosAnterior=array();$DatoDocentes=array();$GetAlumnos_Docentes=array();
        $GetAlumnos_Docentes=MatriculadosAnterior($Distribuidos);
        $AlumnosAnterior=$GetAlumnos_Docentes[0];                             //solo alumnos del anterior semestre
        $DatoDocentes=$GetAlumnos_Docentes[1];                                //solo docentes del anterior semestre

        if ($Option=="distribucion") {
          //
          $Limite=$Limite=(int)((count($Matriculados))/(count($Docentes)-1)); //limite de alumnos por docente, -1 por las filas cabecera
          $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);       //alumnos que sobran,quedan por asignar, atendiendo solo al limite

          $Distribucion_Docente=array();$NoTutorados=array();$NuevosT=array();
          $NuevosT=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);
          $NoTutorados=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);
          $Distribucion_Docente=Distribucion_X_Docente($Distribuidos,$NuevosT,$NoTutorados,$PorAsig,$Limite);
          $filename = 'NuevaDistribucion.csv';
          conversionYdescarga($Distribucion_Docente,$filename);            //descargar Distribucion_Docente con el nombre de filename
        }
        elseif ($Option=="noTutorados") {
          $DatosNo=array();
          $DatosNo=NoTutorados_O_NuevosTut($AlumnosAnterior,$Matriculados);// arreglo de no tutorados
          $filename = 'NoTutorados.csv';
          conversionYdescarga($DatosNo,$filename);                        //descargar con el nombre de filename
        }
        else {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosTut($Matriculados,$AlumnosAnterior);//  arreglo de tutorados
          $filename = 'Tutorados.csv';
          conversionYdescarga($Datos,$filename);                         //descargar co el nombre de filename
        }
      }
    }else {echo "para este propósito3, solo se permite archivo de extencion .csv. Vuelva a cargar";}
  }else {echo "para este propósito2, solo se permite archivo de extencion .csv. Vuelva a cargar";}
}else {echo "para este propósito1, solo se permite archivo de extencion .csv. Vuelva a cargar";}
?>
