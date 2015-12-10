<?php
/***************************************
Script untuk mengambil ongkir Tiki
langsung melalui web tiki-online.com
dengan menggunakan cURL dan html parsing

Copyright (c)2015 by Tanto Prihartanto
Published by Humayraa.com via GitHub

****************************************/


function pangkas($html){
	$hasilnya = $html;
	$hasilnya = str_replace("</td><td width='80%'>", ":", $hasilnya);
	$hasilnya = str_replace("<tr bgcolor=#fedfd1><td width='30%'", "", $hasilnya);
	$hasilnya = str_replace("<tr bgcolor=#fcc2a8><td width='30%'", "", $hasilnya);
	$hasilnya = str_replace("</td></tr>", "", $hasilnya);
	$hasilnya = str_replace(",", "", $hasilnya);
	$hasilnya = str_replace("</td><td _", "", $hasilnya);
	$hasilnya = str_replace("width='80%'>", " Rp ", $hasilnya);
	return $hasilnya;
}

/* CARA GRAB TIKI */

function getKOTA(){
    $hasil = file_get_contents("http://www.tiki-online.com/lib/cariori.php");
	$hasil = explode("\n", $hasil);
	return $hasil;
}


function grabTIKI($dari,$ke,$berat){
	$ch = curl_init();
	$url="http://www.tiki-online.com/?cat=KgfdshfF7788KHfskF";
	$params = "&get_ori=".$dari."&get_des=".$ke."&get_wg=".$berat."&submit=Check";
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_URL, $url);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
	$hasil = curl_exec( $ch );
	return $hasil; 
}

function parseTIKI($html){
	//$htmltiki = file_get_contents($tiki);
	$htmltiki = str_replace(" _\n\t\t"," ", $html);
	$hasil = explode("</table>", $htmltiki);
	$hasil = $hasil[2];
	$hasil = explode("</font></td></tr>",$hasil);
	$hasil = $hasil[1];
	$hasil = explode("align='left'>- ",$hasil);
	//$htmltarrif = str_get_html($hasil);
	$hasilnya ="";
	foreach ($hasil as $key ) {
		# code...
		$hasiltmp = pangkas($key);
		$hasilnya .= $hasiltmp."<br>\n";
	  }
	 return $hasilnya; 
}


$asalkota = $_POST['asal'];
$tujuankota = $_POST['tujuan'];
$berat = $_POST['berat'];
if ($asalkota == ""){
$listKota = getKOTA();
?>

<form action="tiki.php" method="post">
  Dari: <input list="asalkota" name="asal" autocomplete="off">
  <datalist id="asalkota">
  <?php
    foreach ($listKota as $kota) {
    	# code...
    	echo '<option value="'.$kota.'">';
    	echo "\n";
    }
  ?>
  </datalist>
  Ke: <input list="tujuankota" name="tujuan" autocomplete="off">
  <datalist id="tujuankota">
  <?php
    foreach ($listKota as $kota) {
    	# code...
    	echo '<option value="'.$kota.'">';
    	echo "\n";
    }
  ?>
  </datalist>
  Berat:<input type="number" name="berat">
  <input type="submit" value="Cek Tarif">
</form>
<?php }


if ($asalkota !=""){
echo "Asal : ".$asalkota."<br>\n";
echo "Tujuan : ".$tujuankota."<br>\n";
echo "Berat : ".$berat."<br>\n";
	$hasil = grabTIKI($asalkota,$tujuankota,$berat);
//	print_r($hasil);
	$hasil = parseTIKI($hasil);
	echo "<h2>TARIF</h2>";
	echo $hasil;
}
?>
