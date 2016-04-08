<?php
	//Para trabajar en windows:
	//require_once  realpath($_SERVER["DOCUMENT_ROOT"]  . '/firmajs/aplicacion/libreria_interna/_configuracion.php');
	//para trabajar en ubuntu:
	require_once  realpath('../aplicacion/libreria_interna/_configuracion.php');

	session_start();

	date_default_timezone_set('America/Caracas');

	require_once  APP_RUTA . '/vendor/autoload.php';

	$app= new \Slim\Slim( array (
	    'view' => new \Slim\Views\Twig(),
	    'templates.path' => APP_RUTA . '/templates'
	));

	$app->config(array(
	    'debug' => true
	));

	$view = $app->view();

  	$view->parserOptions = array(
      'debug' => true
  	);

  	$view->parserExtensions = array(
      new \Slim\Views\TwigExtension(),
  	);
	
	$app->get('/',function() use($app) {

  		$app->render('inicio.twig');

	})->name('root');

	$app->get('/perfil/:liga/:equipo',function($liga, $equipo) use($app) {
		
		$respuesta=array();

		if(intval($liga) && intval($equipo)):

			$pdo = new \Slim\PDO\Database(DB_DNS_1, DB_USR_1, DB_PWD_1);
				
			$selectStatement = $pdo->select()
	                      ->from('equipos')
	                      ->where('identificador_l', '=', $liga)
	                      ->where('identificador', '=', $equipo);

			$stmt = $selectStatement->execute();
			$data = $stmt->fetch();

			$pdo = null;

			if(empty($data)==false):
				$respuesta['resultado']=true;
				$respuesta['perfil']=$data;

				try {
					$db = new PDO("mysql:host=localhost;dbname=fut_temporada;port=3306","root","jor187239");
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$db->exec("SET NAMES 'utf8'");
				}catch (Exception $e) {
					echo "Error!";
				}

				try {
					$ctrl="SELECT count(*) as 'partidos_l', sum(gol_local) as 'gol_local', sum(gol_visitante) as 'gol_localE', SUM(CASE WHEN gol_local>gol_visitante THEN 1 ELSE 0 END) as 'tp_ganados', SUM(CASE WHEN gol_local=gol_visitante THEN 1 ELSE 0 END) as 'tp_empate' FROM partidos WHERE identificador_l=$liga AND identificador_el=$equipo";
					$verif = $db->query($ctrl);
					$exist = $verif->fetch(PDO::FETCH_ASSOC);
				} catch (Exception $e) {
					echo "ERROR 1:";
					echo $e;
					exit();
				}

				$db = null;

				$respuesta['temporada']['local']=$exist;

				try {
					$db = new PDO("mysql:host=localhost;dbname=fut_temporada;port=3306","root","jor187239");
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$db->exec("SET NAMES 'utf8'");
				}catch (Exception $e) {
					echo "Error!";
				}

				try {
					$ctrl="SELECT count(*) as 'partidos_v', sum(gol_local) as 'gol_visitanteE', sum(gol_visitante) as 'gol_visitante', SUM(CASE WHEN gol_local<gol_visitante THEN 1 ELSE 0 END) as 'tp_ganados', SUM(CASE WHEN gol_local=gol_visitante THEN 1 ELSE 0 END) as 'tp_empate'  FROM partidos WHERE identificador_l=$liga AND identificador_ev=$equipo";
					$verif = $db->query($ctrl);
					$exist = $verif->fetch(PDO::FETCH_ASSOC);
				} catch (Exception $e) {
					echo "ERROR 1:";
					echo $e;
					exit();
				}

				$db = null;

				$respuesta['temporada']['visitante']=$exist;

			else:				
				$respuesta['resultado']=false;
			endif;
		else:
			$respuesta['resultado']=false;
		endif;

	  	$app->render('equipo.twig', array("respuesta"=>$respuesta));

	})->name('perfil');
	//Hasta aqui GET

	$app->get('/buscar/:busqueda',function($busqueda) use($app) {

			$respuesta=array();
			$busqueda_inicial="";

			try {
				$db = new PDO("mysql:host=127.0.0.1;port=9306","root", 'jor187239');
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->exec("SET NAMES 'utf8'");
			}
			catch (Exception $e) {
				echo "Error!";
			}

			if($_GET['busqueda']!=""):

			$busqueda=trim($_GET['busqueda']);
			$busqueda_inicial=$busqueda;
			$busqueda=str_replace(array("    ","   ","  "," "),array(" "," "," ","/"),$busqueda);
			$busqueda=explode("/",$busqueda);

			$consulta_busqueda='';
			$resultados=array();
			
			foreach ($busqueda as $key => $value) {
				if($consulta_busqueda==""):
					$consulta_busqueda= $value;
				else:
					$consulta_busqueda= $consulta_busqueda . "|" . $value;
				endif;
			}

			try {
				$ctrl="SELECT * FROM equipos WHERE MATCH('$consulta_busqueda')";
				$verif = $db->query($ctrl);
				$exist = $verif->fetchAll(PDO::FETCH_ASSOC);
			} catch (Exception $e) {
				echo "ERROR 1:";
				echo $e;
				exit();
			}

			$db = null;

			if(empty($exist)==false):
				$resultados=array_merge ($resultados,$exist);
			endif;

			if(count($resultados)==0):
				$respuesta['resultado']=false;
			else:
				$respuesta['resultado']=true;

					$ligaIng=0;
					$ligaEsp=0;

					foreach ($resultados as $key => $value) {
						if($value['id']<199):
							$ligaEsp=1;
						else:
							$ligaIng=1;
						endif;
					}

					$ligaEsp= array();
					$ligaIng=array();
					$perfiles=array();

					foreach ($resultados as $key => $value) {

						if($value['id']<199):
							$limpieza_idEquipo = $value['id']-100 ;
							$limpieza_idLiga = 1;
							array_push($ligaEsp,$value);
						else:
							$limpieza_idEquipo = $value['id']-200;
							$limpieza_idLiga = 2;
							array_push($ligaIng,$value);
						endif;

						$pdo = new \Slim\PDO\Database(DB_DNS_1, DB_USR_1, DB_PWD_1);
			
						$selectStatement = $pdo->select(array('identificador', 'identificador_l', 'nombre', 'imagen'))
			                       ->from('equipos')
			                       ->where('identificador_l', '=', $limpieza_idLiga)
			                       ->where('identificador', '=', $limpieza_idEquipo);

						$stmt = $selectStatement->execute();
						$data = $stmt->fetchAll();

						$pdo = null;

						$data[0]['nombre_liga']=($value['id']<199) ? "espanola" : "inglesa";

						$perfiles=array_merge($perfiles,$data);
					}


					$respuesta['perfiles']=$perfiles;

					$partidos=array();
					$pre_partidos=array();
					if(count($perfiles)>1):
						$respuesta['comparacion_partidos']=true;
						if(count($ligaEsp)>1 &&  count($ligaIng)>1):
							$respuesta['liga_unica']=false;
						else:
							$respuesta['liga_unica']=true;
						endif;
							if(count($ligaEsp)>=1): 
							foreach ($ligaEsp as $llave_1 => $equipo1) {
								foreach ($ligaEsp as $llave_2 => $equipo2) {
									if($llave_1==$llave_2):
										unset($ligaEsp[$llave_1]);
									else:
										try {
											$db = new PDO("mysql:host=127.0.0.1;port=9306","root", 'jor187239');
											$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
											$db->exec("SET NAMES 'utf8'");
										}
										catch (Exception $e) {
											echo "Error!";
										}

										try {
											$ctrl="SELECT * FROM partidos WHERE MATCH('@identificador_l 1 @identificador_el " . ($equipo1['id']-100) . " | " . ($equipo2['id']-100) . " @identificador_ev " . ($equipo1['id']-100) . " | " . ($equipo2['id']-100) . "')";
											$verif = $db->query($ctrl);
											$exist = $verif->fetchAll(PDO::FETCH_ASSOC);
										} catch (Exception $e) {
											echo "ERROR 1:";
											echo $e;
											exit();
										}

										if(empty($exist)==false):
											foreach ($exist as $key => $value) {
												array_push($pre_partidos,$value);
											}			
										endif;

										$db = null;
									endif;
								}
							}
							endif;

							if(count($ligaIng)>=1): 
							foreach ($ligaIng as $llave_1 => $equipo1) {
								foreach ($ligaIng as $llave_2 => $equipo2) {
									if($llave_1==$llave_2):
										unset($ligaIng[$llave_1]);
									else:
										try {
											$db = new PDO("mysql:host=127.0.0.1;port=9306","root", 'jor187239');
											$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
											$db->exec("SET NAMES 'utf8'");
										}
										catch (Exception $e) {
											echo "Error!";
										}
										// ('" . ($equipo1['id']-100) . " | " . ($equipo2['id']-100) . "')
										try {
											$ctrl="SELECT * FROM partidos WHERE MATCH('@identificador_l 2 @identificador_el " . ($equipo1['id']-200) . " | " . ($equipo2['id']-200) . " @identificador_ev " . ($equipo1['id']-200) . " | " . ($equipo2['id']-200) . "')";
											$verif = $db->query($ctrl);
											$exist = $verif->fetchAll(PDO::FETCH_ASSOC);
										} catch (Exception $e) {
											echo "ERROR 1:";
											echo $e;
											exit();
										}

										if(empty($exist)==false):
											foreach ($exist as $key => $value) {
												array_push($pre_partidos,$value);
											}			
										endif;

										$db = null;
									endif;
								}
							}
							endif;

							foreach ($pre_partidos as $key => $value) {

								if($value['id']<19999):
									$limpieza_idEquipos = $value['id']-10000 ;
									$limpieza_idEquipos=number_format(($limpieza_idEquipos/100), 2, '.', '');
									$limpieza_idEquipos=explode('.', $limpieza_idEquipos);
									$limpieza_idEquipoL = $limpieza_idEquipos[0] ;
									$limpieza_idEquipoV = $limpieza_idEquipos[1] ;
									$limpieza_idLiga = 1;
								else:
									$limpieza_idEquipos = $value['id']-20000 ;
									$limpieza_idEquipos=number_format(($limpieza_idEquipos/100), 2, '.', '');			
									$limpieza_idEquipos=explode('.', $limpieza_idEquipos);
									$limpieza_idEquipoL = $limpieza_idEquipos[0] ;
									$limpieza_idEquipoV = $limpieza_idEquipos[1] ;
									$limpieza_idLiga = 2;
								endif;

								try {
									$db = new PDO("mysql:host=localhost;dbname=fut_temporada;port=3306","root","jor187239");
									$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									$db->exec("SET NAMES 'utf8'");
								}
								catch (Exception $e) {
									echo "Error!";
								}
								
								try {
									$ctrl="SELECT eq1.nombre AS 'local', pt.fecha, eq2.nombre AS 'visitante', pt.gol_local, pt.gol_visitante, pt.identificador_l, pt.identificador_el, pt.identificador_ev, pt.identificador_j FROM partidos pt JOIN equipos eq1, equipos eq2 WHERE pt.identificador_l=" . $limpieza_idLiga . " AND pt.identificador_el=" . $limpieza_idEquipoL ." AND pt.identificador_ev=" . $limpieza_idEquipoV. " AND eq1.identificador_l=" . $limpieza_idLiga . " AND eq1.identificador=" . $limpieza_idEquipoL ." AND eq2.identificador_l=" . $limpieza_idLiga . " AND eq2.identificador=" . $limpieza_idEquipoV;;
									$verif = $db->query($ctrl);
									$exist = $verif->fetch(PDO::FETCH_ASSOC);
								} catch (Exception $e) {
									echo "ERROR 1:";
									echo $e;
									exit();
								}

								$exist['fecha_lista']=date('d/m/Y', strtotime($exist['fecha']));
								$exist['hora_lista']=date('H:i', strtotime($exist['fecha']));
								if($exist["gol_local"]>$exist["gol_visitante"]):
									$exist['ganado']=1;
								else:
									if($exist["gol_local"]<$exist["gol_visitante"]):
										$exist['ganado']=2;
									else:
										if($exist["gol_local"]==$exist["gol_visitante"]):
											$exist['ganado']=0;
										endif;
									endif;
								endif;

								if(empty($exist)==false):
									array_push($partidos,$exist);	
								endif;
							}

							
							$respuesta['partidos']=$partidos;

					else:
						$respuesta['comparacion_partidos']=false;
					endif;
			endif;

			endif;

			$app->render('resultados.twig', array("respuesta"=>$respuesta, "busqueda"=>$busqueda_inicial));
			

	})->name('buscar');
	//Hasta aqui POST
	
	$app->run();