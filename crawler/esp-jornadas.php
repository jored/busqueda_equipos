<?php

include("simple_html_dom.php");

function seccionar_cadena($cadena, $inicio, $fin, $param){

	switch ($param) {
		case 'Ini':
			$posI = 0;
			$posF = strpos($cadena, $fin);

			$posT= strlen($cadena);
			$posP=$posT-$posF;
			$posT=$posT-$posI-$posP;

			return trim(substr($cadena, $posI, $posT));
			break;

		case 'Fin':
			$posI = strpos($cadena, $inicio);
			$posI=$posI+strlen($inicio);

			return trim(substr($cadena, $posI));
			break;

		case 'Set':
			$posI = strpos($cadena, $inicio);
			$posI=$posI+strlen($inicio);
			$posF = strpos($cadena, $fin);

			$posT= strlen($cadena);
			$posP=$posT-$posF;
			$posT=$posT-$posI-$posP;

			return trim(substr($cadena, $posI, $posT));
			break;
	}
}

function obtener_URL($base){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);

	$html_base = new simple_html_dom();

	$html_base->load($str);

	return $html_base;
}

$html_base=obtener_URL("http://resultados.as.com/resultados/futbol/primera/2013_2014/calendario");

$numero=0;
$jornadas=array();


foreach($html_base->find('span[class=dias-cal]') as $element) {
	$jornadas[$numero]['titulo']="Jornada " . ($numero+1);
	$jornadas[$numero]['duracion']=$element->plaintext;
	$numero++;
}

$calendario=array();

foreach ($jornadas as $key => $value) {

	$base = 'http://resultados.as.com/resultados/futbol/primera/2013_2014/jornada/regular_a_' . ($key+1);

	$html_base=obtener_URL($base);

	$partidos=0;

	$calendario[$key+1]['titulo']=$value['titulo'];
	$calendario[$key+1]['duracion']=trim($value['duracion']);
	$calendario[$key+1]['partidos']=array();

	foreach($html_base->find('table[class=clasi-grup] tbody tr') as $element) {
		if(strlen($element->plaintext)>40):
			$calendario[$key+1]['partidos'][$partidos]["equipo_local"]=trim($element->find('td',0)->plaintext);
			$calendario[$key+1]['partidos'][$partidos]["equipo_visitante"]=trim($element->find('td',2)->plaintext);

			$split=explode("-",$element->find('td',1)->plaintext);
			if(empty($split)==false):
				$goles=array();
				$goles['local']=trim($split[0]);
				$goles['visitante']=trim($split[1]);
				$calendario[$key+1]['partidos'][$partidos]["resultado"]=$goles;
			endif;

			$informacion=array();

			$informacion['horario']=trim($element->find('td[class=info-partido] p',0)->plaintext);

			$informacion['estadio']=trim(str_replace("Estadio:", "",$element->find('td[class=info-partido] ul li[class=s-stext-nc]',1)->plaintext));

			$informacion['arbitro']=trim(str_replace("Árbitro:", "",$element->find('td[class=info-partido] ul li[class=s-stext-nc]',0)->plaintext));
			
			$informacion['goles']='';

			$final=$goles['local']+$goles["visitante"];
			if($final>0):
				$x=1;
				do {
					$informacion['goles']=$informacion['goles'] . $element->find('td[class=info-partido] ul li[class=s-stext-nc]',($x+1))->plaintext;
					$x++;
				} while($x<=$final);
			endif;


			$calendario[$key+1]['partidos'][$partidos]["informacion_adicional"]=$informacion;

			$partidos++;

		endif;
	}
}

$salida=json_encode(array_values($calendario));

$fp = fopen('../data/esp-calendario.json', 'w');
fwrite($fp, $salida);
fclose($fp);

?>