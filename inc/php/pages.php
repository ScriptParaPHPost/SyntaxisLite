<?php 
/**
 * Controlador
 *
 * @name    pages.php
 * @author  PHPost Team
*/
/**********************************\

*	(VARIABLES POR DEFAULT)		*

\*********************************/

	$tsPage = "pages";	// tsPage.tpl -> PLANTILLA PARA MOSTRAR CON ESTE ARCHIVO.

	$tsLevel = 0;		// NIVEL DE ACCESO A ESTA PAGINA. => VER FAQs

	$tsAjax = empty($_GET['ajax']) ? 0 : 1; // LA RESPUESTA SERA AJAX?
	
	$tsContinue = true;	// CONTINUAR EL SCRIPT
	
/*++++++++ = ++++++++*/

	include "../../header.php"; // INCLUIR EL HEADER

	$tsTitle = $tsCore->settings['titulo'].' - '.$tsCore->settings['slogan']; 	// TITULO DE LA PAGINA ACTUAL

/*++++++++ = ++++++++*/

	// VERIFICAMOS EL NIVEL DE ACCSESO ANTES CONFIGURADO
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1){	
		$tsPage = 'aviso';
		$tsAjax = 0;
		$smarty->assign("tsAviso",$tsLevelMsg);
		//
		$tsContinue = false;
	}
	//
	if($tsContinue){

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/
    $action = $_GET['action'];
/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/

    switch($action){
      case 'protocolo':
      case 'terminos':
      case 'privacidad':
      case 'dmca':
		case 'portada':
      break;
		case 'settings':
			if($tsUser->is_admod !== 1) header("Location: {$tsCore->settings['url']}");
			$smarty->assign('tsTab', (isset($_GET['tab']) ? $_GET['tab'] : 'seo'));
		break;
      default:
      	$tsCore->redirectTo($tsCore->settings['url']);
      break;
   }
   //
   $smarty->assign("tsAction",$action);
/**********************************\

* (AGREGAR DATOS GENERADOS | SMARTY) *

\*********************************/
	}

if(empty($tsAjax)) {	// SI LA PETICION SE HIZO POR AJAX DETENER EL SCRIPT Y NO MOSTRAR PLANTILLA, SI NO ENTONCES MOSTRARLA.

	$smarty->assign("tsTitle",$tsTitle);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL

	/*++++++++ = ++++++++*/
	include TS_ROOT . 'footer.php';
	/*++++++++ = ++++++++*/
}