<?php if ( ! defined('TS_HEADER')) exit('No se permite el acceso directo al script');
/**
 * Controlador AJAX
 *
 * @name    ajax.live.php
 * @author  PHPost Team
*/
/**********************************\

*	(VARIABLES POR DEFAULT)		*

\*********************************/

	// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCI�N
	$files = array(
		'live-stream' => array('n' => 2, 'p' => 'stream'),
      'live-vcard' => array('n' => 0, 'p' => 'vcard'),
      'live-header' => array('n' => 0, 'p' => ''),
      'live-seo' => array('n' => 0, 'p' => ''),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.live.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg['mensaje']; die();}
	// CODIGO
	switch($action){
		case 'live-stream':
			//<---
         // NOTIFICACIONES
         if($_POST['nots'] != 'OFF') {
            $tsStream = $tsMonitor->getNotificaciones(true);
            $smarty->assign("tsStream", $tsStream);
         }
         // MENSAJES
         if($_POST['mps'] != 'OFF') {
            $tsMensajes = $tsMP->getMensajes(1, true, 'live'); // Edit: 21/02/2014
            $smarty->assign("tsMensajes", $tsMensajes);   
         }
			//--->
		break;
		case 'live-vcard':
			//<---
         # LOCALES
         $user_id = $_REQUEST['uid'];
         # PROCESOS
         $smarty->assign("tsData", $tsUser->getVCard($user_id));
			//--->
		break;
		case 'live-header':
			# Obtiene la lista de archivos .webp en la carpeta
			$where = isset($_POST['type']) ? htmlspecialchars($_POST) : '';
			if(!empty($where)) {
				$carpeta = ($where === 'uploads') ? TS_UPLOADS : TS_DOWNLOADS;
				$archivosWebp = glob($carpeta . '/*.webp');
				// Elimina cada archivo
				foreach ($archivosWebp as $archivo) unlink($archivo);
			}			
			echo $tsJson->save_json('background');
		break;
		case 'live-seo':
			echo $tsJson->save_json('seo');
		break;
      default:
         die('0: Este archivo no existe.');
      break;
	}