<?php if ( ! defined('TS_HEADER')) exit('No se permite el acceso directo al script');
/**
 * Controlador AJAX
 *
 * @name    ajax.portal.php
 * @author  PHPost Team
*/
/**********************************\

*	(VARIABLES POR DEFAULT)		*

\*********************************/

	// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCI�N
	$files = array(
		'portal-posTS_SETTINGSig' => array('n' => 2, 'p' => ''),
        'portal-posts_pages' => array('n' => 2, 'p' => 'posts'),
        'portal-favs_pages' => array('n' => 2, 'p' => 'posts'),
        'portal-activity_pages' => array('n' => 2, 'p' => 'actividad'),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.portal.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg['mensaje']; die();}
    // CLASS
    include TS_CLASS . 'c.portal.php';
    $tsPortal = new tsPortal();
    //
	// CODIGO
	switch($action){
		case 'portal-posTS_SETTINGSig':
			//<---
                echo $tsPortal->savePostsConfig();
			//--->
		break;
		case 'portal-posts_pages':
			//<---
            $tsPosts = $tsPortal->getMyPosts();
            $smarty->assign("tsPosts",$tsPosts['data']);
            $smarty->assign("tsPages",$tsPosts['pages']);
            $smarty->assign("tsType",'posts');
			//--->
		break;
		case 'portal-favs_pages':
			//<---
            $tsPosts = $tsPortal->getFavorites();
            $smarty->assign("tsPosts",$tsPosts['data']);
            $smarty->assign("tsPages",$tsPosts['pages']);
            $smarty->assign("tsType",'favs');
			//--->
		break;
		case 'portal-activity_pages':
			//<---
            $actividad = $tsActividad->getActividadFollows();
            if(!is_array($actividad)) die('<div class="emptyData">'.$actividad.'</div>');
            $smarty->assign("tsActividad",$actividad);
            $smarty->assign("tsUserID",$user_id);
			//--->
		break;
        default:
            die('0: Este archivo no existe.');
        break;
	}
?>