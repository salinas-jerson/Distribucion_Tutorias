<?php
include("alumno.php");
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
<<<<<<< HEAD
//
=======
// 
>>>>>>> e8eab47e1c8fd6928d8a2aaf6c1d0261e33f6387


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
          $Limite=0;
          $PorAsig=0;
          $Distribucion_Docente=array();
          $Distribucion_Docente=Distribucion_X_Docente($Distribuidos,$Matriculados,$Docentes,$AlumnosAnterior,$DatoDocentes,$PorAsig,$Limite);
          //
          printf($PorAsig." Docentes tendran mas 1 alumno <br>");
          printf("N° Matriculados ".(count($Matriculados)).", N° DocenteS ".(count($Docentes)-1).",  Asignacion a ".$Limite." alumnos"."<br><br>");
          //
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";
          //echo .... cabeceras de la tabla
          Mostrar($Distribucion_Docente);
        }
        elseif ($Option=="noTutorados") {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosT($AlumnosAnterior,$Matriculados);// en datos está todo un distribuidos
          //
          echo "Alumnos no tutorados :-) <br><br>";
          //echo .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";

          Mostrar($Datos);//mostar alumnos no turorados
        }
        else {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosT($Matriculados,$AlumnosAnterior);// en datos está todo un distribuidos
          //
          echo "Alumnos tutorados :)<br> <br>";
          //echo .... cabeceras de la tabla
          echo "<table>"."<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>"."</table>";

          Mostrar($Datos);//alumnos tutorados
        }


      }
      //otro boton
<<<<<<< HEAD
      // segundo boton
=======
      // segundo boton 
>>>>>>> e8eab47e1c8fd6928d8a2aaf6c1d0261e33f6387
      elseif ($BotonDescargar) {
        $AlumnosAnterior=array();$DatoDocentes=array();$GetAlumnos_Docentes=array();
        $GetAlumnos_Docentes=MatriculadosAnterior($Distribuidos);
        $AlumnosAnterior=$GetAlumnos_Docentes[0];//solo alumnos del anterior semestre
        $DatoDocentes=$GetAlumnos_Docentes[1];//solo docentes del anterior semestre

        if ($Option=="distribucion") {
          $Limite=0;
          $PorAsig=0;
          $Distribucion_Docente=array();
          $Distribucion_Docente=Distribucion_X_Docente($Distribuidos,$Matriculados,$Docentes,$AlumnosAnterior,$DatoDocentes,$PorAsig,$Limite);
          //Mostrar($Distribucion_Docente);
<<<<<<< HEAD

          $filename = 'NuevaDistribucion.csv';
          header("Content-type: text/csv");
          header("Content-Disposition: attachment; filename=$filename");
          $output = fopen("php://output", "w");

          foreach($Distribucion_Docente as $row)
          {
            fputcsv($output, array($row[0],$row[1]));
          }
          fclose($output);

        }
        elseif ($Option=="noTutorados") {
          $DatosNo=array();
          $DatosNo=NoTutorados_O_NuevosT($AlumnosAnterior,$Matriculados);// en datos está todo un distribuidos

          $filename = 'NoTutorados.csv';
          header("Content-type: text/csv");
          header("Content-Disposition: attachment; filename=$filename");
          $output = fopen("php://output", "w");

          foreach($DatosNo as $row)
          {
            fputcsv($output, array($row[0],$row[1]));
            //fputcsv($output, $row);
          }
          fclose($output);
        }
        else {
          $Datos=array();
          $Datos=NoTutorados_O_NuevosT($Matriculados,$AlumnosAnterior);// en datos está todo un distribuidos


          $filename = 'Tutorados.csv';
          header("Content-type: text/csv");
          header("Content-Disposition: attachment; filename=$filename");
          $output = fopen("php://output", "w");

          foreach($Datos as $row)
          {
            fputcsv($output, array($row[0],$row[1]));
            //fputcsv($output, $row);
          }
          fclose($output);
        }
      }


=======
          
              
          $filename = 'NuevaDistribucion.csv';       
          header("Content-type: text/csv");       
          header("Content-Disposition: attachment; filename=$filename");       
          $output = fopen("php://output", "w");       
  
          foreach($Distribucion_Docente as $row)       
          {  
            fputcsv($output, array($row[0],$row[1]));  
          }       
          fclose($output);    
          
        }
        elseif ($Option=="noTutorados") {
          $DatosNo=array();
          $DatosNo=NoT_yNuevos($AlumnosAnterior,$Matriculados);// en datos está todo un distribuidos
          
          $filename = 'NoTutorados.csv';       
          header("Content-type: text/csv");       
          header("Content-Disposition: attachment; filename=$filename");       
          $output = fopen("php://output", "w");       
    
          foreach($DatosNo as $row)       
          {  
            fputcsv($output, array($row[0],$row[1]));  
            //fputcsv($output, $row);  
          }       
          fclose($output);  
        }
        else {
          $Datos=array();
          $Datos=NoT_yNuevos($Matriculados,$AlumnosAnterior);// en datos está todo un distribuidos
          
  
          $filename = 'Tutorados.csv';       
          header("Content-type: text/csv");       
          header("Content-Disposition: attachment; filename=$filename");       
          $output = fopen("php://output", "w");       

          foreach($Datos as $row)       
          {  
            fputcsv($output, array($row[0],$row[1]));  
            //fputcsv($output, $row);  
          }       
          fclose($output);  
        }
      }

      
>>>>>>> e8eab47e1c8fd6928d8a2aaf6c1d0261e33f6387
    }else {echo "para este propósito3, solo se permite archivo de extencion .csv";}
  }else {echo "para este propósito2, solo se permite archivo de extencion .csv";}
}else {echo "para este propósito1, solo se permite archivo de extencion .csv";}
?>
