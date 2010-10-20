<?php
/** 
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Library General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*
* (C) Copyright 2008-2009 Thunder Cache
*
* For more information check http://thundercache.org
*
* Get relatories from system
*
* @author Joaquim Pedro (osmano807) <osmano807@gmail.com>
  modified by Rodrigo Manga
*/ 

	error_reporting(0);
	$cache_dir = "/thunder";

	if (!( $db = new PDO('mysql:host=localhost;dbname=thunder', 'root','thundercache') ) ) {
		die("N�o consegui conectar no database");
	}
	
	function disk_use($dir){
		$df = disk_free_space($dir);
		$dt = disk_total_space($dir);
		$du = $dt-$df;
		return ($du / $dt)*100;
	}

	function sizeFormat($size){
	    if($size<1024)
	    {
	        return $size." bytes";
	    }
	    else if($size<(1024*1024))
	    {
	        $size=round($size/1024,1);
	        return $size." KiB";
	    }
	    else if($size<(1024*1024*1024))
	    {
	        $size=round($size/(1024*1024),1);
	        return $size." MiB";
	    }
	    else
	    {
	        $size=round($size/(1024*1024*1024),1);
	        return $size." GiB";
	    }

	}

	$totaleconomy = $totalhits = $totalcount = $totalsize = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  	
<head>
		<title>Relatorio Thunder Cache</title>
	<style type="text/css">
		<!--
		table {
			border: 1px solid #333333;
			border-collapse:collapse;
			height:20px;
			text-align:center;
			text-transform: none;
		}

		td {
			padding-right: 30px;
			padding-left: 30px;
			height:20px;	
		}

		tr {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			text-decoration: none;	
			height:25px;	
		}

		.cabecalho {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
			text-transform: uppercase;
			color: #FFFFFF;
			text-decoration: none;
			height:25px;
			background-color: #666666;
		}
		body {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight: bold;
			text-transform: uppercase;
			text-align:center;	
		}
		-->
	</style>
	</head>
	<body>
	<div align="center"><font color="#0C70EE"><strong>Relat&oacute;rio do Sistema Thunder Cache 3:</strong><br />
	</font><br />
	<strong>Data de gera&ccedil;&atilde;o: <?= date(DATE_RFC822) ?>
	<br>
	Uso do disco: <?= round(disk_use($cache_dir),2) ?> %
	</strong></div><br>
	<table border="1" align="center">
	 <tr class="cabecalho">
	   <td><strong>Dom&iacute;nio</strong></td>
	   <td><strong>Arquivos</strong></td>
	   <td><strong>Tamanho</strong></td>
	   <td><strong>Economia</strong></td>
	   <td><strong>Hits</strong></td>
	   <td><strong>Efici&ecirc;ncia %</strong></td></tr>
	<?
	global $db;
    $query = "select domain,COUNT(*) as files,sum(size) as size,sum(size*requested) as eco, sum(requested) as hits from thunder where deleted=0 and static=0 group by domain UNION select 'static' as domain,COUNT(*) as files,sum(size) as size,sum(size*requested) as eco, sum(requested) as hits from thunder where deleted=0 and static=1";
    foreach ($db->query($query) as $valor) {
        $percent=round(($valor['eco']/$valor['size'])*100,2)
?>
		<tr>
		  <td height="18"><font color="#20A253">
		    <?= $valor['domain'] ?>
		  </font></td>
		<td height="18"><font color="#20A253"><?=  $valor['files'] ?></font></td>
		<td height="18"><font color="#20A253"><?= sizeFormat($valor['size']) ?></font></td>
		<td height="18"><font color="#20A253"><?= sizeFormat($valor['eco']) ?></font></td>
		<td height="18"><font color="#20A253"><?= $valor['hits'] ?></font></td>
		<td height="18"><font color="#20A253"><?= $percent ?> %</td></tr>
		<?
		$totaleconomy += $valor['eco'];
		$totalhits += $valor['hits'];
		$totalcount += $valor['files'];
		$totalsize += $valor['size'];		
	}
?> 
  <tr><td height="22"><b><font color="#0C70EE">Total</font></b></td>
	<td height="22"><b><font color="#0C70EE"><?= $totalcount ?></font></b></td>
	<td height="22"><b><font color="#0C70EE"><?= sizeFormat($totalsize) ?></font></b></td>
	<td height="22"><b><font color="#0C70EE"><?= sizeFormat($totaleconomy) ?></font></b></td>
	<td height="22"><b><font color="#0C70EE"><?= $totalhits ?></font></b></td>
	<td height="22"><b><font color="#0C70EE"><?= round(($totaleconomy/$totalsize)*100,3) ?> %</font></b></td></tr>
 </table><br>
 <table border="1" align="center"><tr><td align="left"<pre>
 <?php system("free -om"); ?>
 </pre></td></tr></table><br>
 <table border="1" align="center"><tr><td align="left"><pre>
 <?php system("uptime"); ?></td>
 </pre></td></tr></table><br>
 <table border="1" align="center"><tr><td align="left"><pre>
 <?php system("cat /proc/cpuinfo | grep \"model name\\|processor\""); ?>
 </pre></td></tr></table>
</body>
</html>
