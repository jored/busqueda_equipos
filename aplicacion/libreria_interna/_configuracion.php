<?php
	//Para trabajar en windows:
	/*
	defined("SERVIDOR_RUTA")
	    or define("SERVIDOR_RUTA", realpath($_SERVER["DOCUMENT_ROOT"] . "/firmajs"));
	defined("DB_DNS_1")
	    or define("DB_DNS_1", 'mysql:host=localhost;dbname=fut_temporada;charset=utf8;port=3306');
	defined("DB_USR_1")
	    or define("DB_USR_1", 'root');
	defined("DB_PWD_1")
	    or define("DB_PWD_1", '');
	*/
	//Para trabajar en Ubuntu:
	
	defined("SERVIDOR_RUTA")
	    or define("SERVIDOR_RUTA", realpath('..'));
	defined("DB_DNS_1")
	    or define("DB_DNS_1", 'mysql:host=localhost;dbname=fut_temporada;charset=utf8;port=3306');
	defined("DB_USR_1")
	    or define("DB_USR_1", 'root');
	defined("DB_PWD_1")
	    or define("DB_PWD_1", 'jor187239');

	defined("APP_RUTA")
	    or define("APP_RUTA", realpath(SERVIDOR_RUTA . "/aplicacion"));
	defined("APP_FIRMAS")
	    or define("APP_FIRMAS", realpath(SERVIDOR_RUTA . "/web/firmas"));
	defined("APP_ADJUNTOS")
	    or define("APP_ADJUNTOS", realpath(SERVIDOR_RUTA . "/web/adjuntos"));