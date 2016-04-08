<?php

include("simple_html_dom.php");



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

$html_base=obtener_URL("http://resultados.as.com/resultados/futbol/inglaterra/2013_2014/equipos/");

$er=1;

$equipos=array();

foreach($html_base->find('a[class=col-md-6 col-sm-6 col-xs-6 content-info-escudo]') as $element) {

	$ruta=split("/", $element->href);
	$equipos[$er]=array();
	$equipos[$er]['id']=$er;
	$equipos[$er]['ruta_perfil']="http://resultados.as.com/resultados/ficha/equipo/" . $ruta[4] . "/" . $ruta[5] . "/";
	$equipos[$er]['imagen_escudo']=$element->find('span.escudo img', 0)->src;
	$equipos[$er]['nombre']=$element->find('span.escudo', 0)->plaintext;

	$html_base2=obtener_URL("http://resultados.as.com/resultados/ficha/equipo/" . $ruta[4] . "/" . $ruta[5] . "/");

	$caracteristicas=array();
	$llave='';

	foreach($html_base2->find('section[[class=hdr-ficha] dl dt, section[[class=hdr-ficha] dl dd') as $equipo) {	
		if($llave==''):
			$llave=strtolower(trim(str_replace(":", "", $equipo->plaintext)));

			$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
			$llave = strtr( $llave, $unwanted_array );
		else:
			$caracteristicas[][$llave]=$equipo->plaintext;
			$llave='';
		endif;
	}

	$equipos[$er]['caracteristicas']=$caracteristicas;

	$er++;
}

$salida=json_encode(array_values($equipos));

$fp = fopen('data/ing-equipos.json', 'w');
fwrite($fp, $salida);
fclose($fp);
?>