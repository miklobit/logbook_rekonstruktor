<?php

$display_form = 0;
if( isset($_POST['source'] ) )
{
 if( $_POST['source'] == "gpx" && isset( $_FILES["file"]["name"])  ) {	
	// echo 'posted gpx';
	
	
	//    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
	//    echo "Type: " . $_FILES["file"]["type"] . "<br />";
	//    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
	//    echo "Stored in: " . $_FILES["file"]["tmp_name"];	
	    
	if (   ($_FILES["file"]["type"] == "application/gpx")
	    && ($_FILES["file"]["size"] < 200000)
	   )  {
	    if ($_FILES["file"]["error"] > 0)
	    {
	    echo "Error: " . $_FILES["file"]["error"] . "<br />";
	    }
	    else
	    {
		   generujLog($_FILES["file"]["tmp_name"]);
		   unlink( $_FILES["file"]["tmp_name"] );
	    }
	}
	else {
	  echo "Nieprawidłowa nazwa/typ/wielkość pliku";
	}
   	
	
 } elseif( $_POST['source'] == "waypoint" ) {
	$wp = $_POST['wp'];
	
	// generate xsl transformation
	
	$url = 'http://opencaching.pl/search.php?searchto=searchbywaypoint&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&startat=0&waypoint=' ;
	$url .= $wp ;
	$url .= '&output=gpxgc' ;
	
	generujLog($url);
	
 } 
   else {  $display_form = 1; }
} else { $display_form = 1; }


if( $display_form  != 0 ) {
// display form
echo '<html>
	<head>
	<title>Logbook rekonstruktor</title> 
	<style type="text/css">
	 body { font-family: verdana,tahoma ; font-size: 10px ; }
	 form { font-size: 12px; }
	</style>
	<body id="top">
	<h2>Logbook rekonstruktor</h2><br/>
	<form name="input" action="" method="post" enctype="multipart/form-data">
	
	  <input type="radio" name="source" value="waypoint" checked="checked" /> 
	   Kod skrzynki z <a href="http://opencaching.pl">Opencaching.pl</a>
	   <input type="text" name="wp" value="OP0001" /><br /><br />
	   <hr width="30%" align="left" />
	   
	  <input type="radio" name="source" value="gpx" />  
	   Plik gpx (format gpx-gc, max. wielkość 200Kb)<br />
      <label for="file">Nazwa pliku</label>
      <input type="file" name="file" id="file" /><br /><br />   
	  <hr width="30%" align="left" />    
      
	  <input type="checkbox" name="include_nam" value="1" checked="checked" />Pokaż nazwę skrzynki<br />
	  <input type="checkbox" name="show_dat" value="1" checked="checked" />Pokaż datę założenia<br />	  
	  <input type="checkbox" name="include_dnf" value="1" checked="checked" />Pokaż dnf' ."'". 'y <br />
	  <input type="checkbox" name="show_icon" value="1" checked="checked" />Pokaż ikony<br />
	  <input type="checkbox" name="include_txt" value="1" checked="checked" />Pokaż tekst wpisu<br />
	  Szerokość logu <input type="text" name="log_width" value="400" />px<br />
	  
	  <hr width="30%" align="left" />  	  
	   <input type="submit" name="submit" value="Generuj log" />
	</form> 

	</body>
	</html>';
    
}

function generujLog($url)
{
	$show_dat = isset($_POST['show_dat']) ? "1" : "0" ;
	$include_nam = isset($_POST['include_nam']) ? "1" : "0" ;
	$include_dnf = isset($_POST['include_dnf']) ? "1" : "0" ;
	$show_icon = isset($_POST['show_icon']) ? "1" : "0" ;
	$include_txt = isset($_POST['include_txt']) ? "1" : "0" ;
	
	
	
	
	// Load the XML source
	$xml = new DOMDocument;
	$xml->load($url);
	//$xml->load('test.gpx');
	$xsl = new DOMDocument;
	$xsl->load('logbook.xsl');
		
	// Configure the transformer
	$proc = new XSLTProcessor;
	
	// set parameters
	$proc->setParameter( '', 'show_dat',  $show_dat);	
	$proc->setParameter( '', 'include_nam',  $include_nam);	
	$proc->setParameter( '', 'include_dnf',  $include_dnf);	
	$proc->setParameter( '', 'show_icon',  $show_icon);	
	$proc->setParameter( '', 'include_txt',  $include_txt);	
	if( isset($_POST['log_width']) ) {
		$proc->setParameter( '', 'log_width',  $_POST['log_width'] );
	}		
		
	$proc->importStyleSheet($xsl); // attach the xsl rules
	echo $proc->transformToXML($xml);
}


?>
