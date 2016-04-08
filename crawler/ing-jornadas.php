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

$html_base=obtener_URL("http://resultados.as.com/resultados/futbol/inglaterra/2013_2014/calendario");

$numero=0;
$jornadas=array();


foreach($html_base->find('span[class=fecha-evento]') as $element) {
	$jornadas[$numero]['titulo']="Jornada " . ($numero+1);
	$jornadas[$numero]['duracion']=$element->plaintext;
	$numero++;
}

$calendario=array();

foreach ($jornadas as $key => $value) {
	
	$base = 'http://resultados.as.com/resultados/futbol/inglaterra/2013_2014/jornada/regular_a_' . ($key+1);

	$html_base=obtener_URL($base);

	$separador=0;
	$partidos=0;
	$llave="";
	$contenido='';

	$calendario[$key+1]['titulo']=$value['titulo'];
	$calendario[$key+1]['duracion']=$value['duracion'];
	$calendario[$key+1]['partidos']=array();


	foreach($html_base->find('td') as $element) {
		$contenido=trim($element->plaintext);
		if(isset($element->attr['class'])):
			if($element->attr['class']=="txt-al-r"):
				$llave="equipo_local";
			endif;
			if($element->attr['class']=="txt-al-l"):
				$llave="equipo_visitante";
			endif;
			if($element->attr['class']=="gray txt-al-l info-partido"):
				$llave="informacion_adicional";

				$informacion=array();
	    		
				$texto_completo=$element->plaintext;
				$informacion['horario']=seccionar_cadena($texto_completo, "", "Más", "Ini");
				$informacion['estadio']=seccionar_cadena($texto_completo, "Estadio:", "Gol", "Set");
				$informacion['arbitro']=seccionar_cadena($texto_completo, "Árbitro:", "Estadio:", "Set");
				$informacion['goles']=seccionar_cadena($texto_completo, "Goles", "", "Fin");

				$contenido= $informacion;

			endif;
		else:
			$llave="resultado";
			$split=explode("-",$element->plaintext);
			$goles=array();
			$goles['local']=trim($split[0]);
			$goles['visitante']=trim($split[1]);
			$contenido=$goles;
		endif;
		$separador++;
		$calendario[$key+1]['partidos'][$partidos][$llave]=$contenido;
		if($separador==4):
			$separador=0;
			$partidos++;
		endif;
	}
}

$salida=json_encode(array_values($calendario));

$fp = fopen('calendario.json', 'w');
fwrite($fp, $salida);
fclose($fp);


?>