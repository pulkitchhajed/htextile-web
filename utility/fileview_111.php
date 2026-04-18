<?php include("../includes/check_session.php");
include("../includes/config.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bill View </title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />

</head>
<script>

    function downloadBackup(){
        document.getElementById('download').action='backup_download.php';
        //document.getElementById('download').target="_new";
        document.getElementById('download').submit();


    }
</script>
<body>
<form method="post" id="download" enctype="multipart/form-data" > 
<table width="100%" border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">
<tr>
    <td><?php include("../includes/header.php"); ?></td>
  </tr>
  <tr>
    <td><?php include("../includes/menu.php"); ?></td>
  </tr>
</table>
<table width="100%">
  <tr><td style="font-size: 12px;color: #ffffff;background-color:#ffffff" align='center' width="100%">

</td>
</tr>
<tr> 
  <td>
<?php


$reqDir1="";
if(isset($_REQUEST['NextDir1'])){
  //echo "dir 1 Set";
  $reqDir1=$_REQUEST['NextDir1'];
  //echo $reqDir1;
  
}

$reqDir2="";
if(isset($_REQUEST['NextDir2'])){
  //echo "dir 2 Set";
  $reqDir2=$_REQUEST['NextDir2'];
 // echo $reqDir2;
}

$reqDir3="";
if(isset($_REQUEST['NextDir3'])){
  //echo "dir 3 Set";
  $reqDir3=$_REQUEST['NextDir3'];
  //echo $reqDir3;
}

//echo dirname("/backup/textile_backup_01_09_2022_10_39_43.sql");
echo "<br>";

//echo getcwd();

echo "<br>";

chdir('../../');
chdir ('bill_images');

if($reqDir1!="")
{
  //echo getcwd();
  //echo "Dir 1".$reqDir1;
  chdir($reqDir1);
  //echo "Dir 1111111111";
  if ($reqDir2!=""){
    //echo "Dir 2";
    chdir($reqDir2);

    if ($reqDir3!=""){
      //echo "Dir 3";
      chdir($reqDir3);
    }
  
  }

}

$folderDetails=(glob('*'));

//print_r($folderDetails);

echo " <br>";
/*
$rootdir=require_once($_SERVER['DOCUMENT_ROOT']);

echo " Server Root -";
echo $rootdir;
echo "--";
echo " <br>";
*/

?>
<table border=1> <tr> <td align=center> 
<?php

echo '<a href=fileview.php > <img border="0" alt="Folder" src="../images/home.png" width="100" height="100">  </a>';
echo "<br>";
echo "<a href=fileview.php> Home </a>";
echo "<br>";
?>

</td></tr>

<tr> <td align=center>
<?php 

if ($reqDir1 !=""){

  ?>
<table border=2> <tr> <td align=center>
  <?php 
  // <img border="0" alt="W3Schools" src="logo_w3s.gif" width="100" height="100">
  echo '<a href=fileview.php?NextDir1='.$reqDir1.' > <img border="0" alt="Folder" src="../images/yearfolder2.png" width="100" height="100">  </a>';
  echo "<br>";
  echo "<a href=fileview.php?NextDir1=$reqDir1 > $reqDir1 </a>";
  ?>

  <?php
  if ($reqDir2 !=""){

    ?>

  </td>  <td align=center>
    <?php 
    

    echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$reqDir2 > ";
    echo '<img border="0" alt="Folder" src="../images/monthfolder2.png" width="100" height="100">';
    echo " </a>";

    echo " <br>";
    echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$reqDir2 > $reqDir2 </a>";

    if ($reqDir3 !=""){ ?>

    </td> <td align=center>

    <?php




      echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$reqDir2"."&NextDir3=$reqDir3 > ";
      echo '<img border="0" alt="Folder" src="../images/dayfolder2.png" width="100" height="100">';
      echo " </a>";
  
      echo " <br>";
      echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$reqDir2"."&NextDir3=$reqDir3 > $reqDir3 </a>";
      


    }
    ?>
    </td> </tr> </table>
<?php
  }
}

?>

</td>
</tr>

<tr> 
  <td  align=center>
  <table border=2>
  <tr> 

<?php 
$count=0;
$folder_count=-1;
foreach ($folderDetails as $value) {
  ?>


  <td  align=center>

  <?php 
  $dispValue=$value;
  if(is_dir($dispValue)){ ?>

    
  
  <?php 
  $folder_count++;
    if ($reqDir1 !=""){
      if ($reqDir2 !=""){
            //echo "<b>"."$dispValue  "." </b> <br>" ;

            if(is_int($folder_count/10)){
              ?>
              </td></tr><tr><td align=center> 
            <?php

            }            
            echo "<br>" ;
            //echo "Day";
            //echo $folder_count;            
            echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$reqDir2"."&NextDir3=$dispValue >";
            echo '<img border="0" alt="Folder" src="../images/Folder.png" width="100" height="100"> ';
            echo "</a>";
            echo "<br>" ;
            echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$reqDir2"."&NextDir3=$dispValue > $dispValue </a>";


      }else{
      //echo "<b>"."$dispValue  "." </b> <br>" ;

      if(is_int($folder_count/6)){
        ?>
        </td></tr><tr><td align=center> 
      <?php

      }        

      echo "<br>" ;
      //echo "Month";
      //echo $folder_count;
      echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$dispValue >";
      echo '<img border="0" alt="Folder" src="../images/Folder.png" width="100" height="100"> ';
      echo "</a>";
      echo "<br>" ;
      echo "<a href=fileview.php?NextDir1=$reqDir1"."&NextDir2=$dispValue > $dispValue </a>";
      }


    }else {
      //echo "<b>"."$dispValue  "." </b> ";
      echo "<br>" ;
      //echo "YEAR ";
      //echo $folder_count;

      echo "<a href=fileview.php?NextDir1=$dispValue > ";
      echo '<img border="0" alt="Folder" src="../images/Folder.png" width="100" height="100"> ';
      echo "</a>";

      echo "<br>" ;
      echo "<a href=fileview.php?NextDir1=$dispValue > $dispValue </a>";
    }
  
    // Open a directory, and read its contents
    /*
if (is_dir($dispValue)){
  if ($dh = opendir($dispValue)){
    while (($file = readdir($dh)) !== false){
      echo "filename:" . $file . "<br>";
    }
    closedir($dh);
  }
}

*/
  } else {
 
  //echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;"."$dispValue <br>";

  //  This is Table Row  Breaker for Bill Images.
  $count++;
  //echo " Count ".$count;
    if(!is_int($count/2)){ 
     // echo " Count ".$count;
      ?>
      </td></tr><tr><td align=center> 
    <?php
    }

  $slen=strlen($dispValue) ;
  $strext=substr($dispValue,($slen-4),4 );
  // echo  $strext;
  // chdir()
  // getcwd()  -- Current Working Directory
  // substr()
if ($strext==".jpg" || $strext==".JPG" ){
  echo "&nbsp;";
 echo" <img src='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue' alt='Bill' width='500' height='600'>";
 //echo "<object data='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue'  width='500' height='600'> ";

 //echo "<iframe src='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue'  width='500' height='600'> </iframe>";
 echo " <br>";echo " <br>";
 echo "<a href='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue' > $dispValue </a> ";
 // <a href="/images/myw3schoolsimage.jpg" download>
 echo " <br>";
} else if ($strext==".PDF" ||  $strext==".pdf" ) {
  echo "&nbsp;";
  echo" <img src='../images/pdfdownload.png' alt='Bill' width='50' height='50'>";

  echo "<object data='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue'  width='800' height='500'> ";
 
  echo " <br>";echo " <br>";
  echo "<a href='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue' > $dispValue </a> ";
  echo " <br>";

} else if ($strext==".DOC" ||  $strext==".doc" || $strext=="DOCX" || $strext=="docx" ) {

  echo "&nbsp;";
  echo" <img src='../images/worddocument.png' alt='Bill' width='50' height='50'>";
  echo "&nbsp;";
  echo "<object  data='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue'  width='800' height='500'> ";
  echo "&nbsp;";
  echo " <br>";echo " <br>";
  echo "<a href='../../bill_images/$reqDir1/$reqDir2/$reqDir3/$dispValue' > $dispValue </a> ";
  echo " <br>";

  // <img src="img_girl.jpg" alt="Girl in a jacket" width="500" height="600">
}
  ?>
 
<?php
}

  ?>
 </td> 
  <?php

}




?>
</tr>

</table></td></tr>
</table>
</td>
</tr>
</table>
</form>
</body>
</html>
