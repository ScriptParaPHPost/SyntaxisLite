<?php

if (!defined('TS_HEADER'))
	 exit('No se permite el acceso directo al script');
/**
 * Modelo para la adminitración
 *
 * @name    c.socials.php
 * @author  PHPost Team
 */
class tsSocials {

	public function getSocials() {
		global $tsCore;
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', 'SELECT social_id, social_name, social_client_id, social_client_secret, social_redirect_uri FROM w_social'));
		foreach($data as $key => $social) {
			$data[$key]['social_redirect_uri'] = $tsCore->settings['url'] . '/' . $social['social_name'] . '.php';
		}
		return $data;
	}

	public function newSocial() {
		global $tsCore;
		foreach($_POST = (isset($_POST['save']) ? array_slice($_POST, 0, -1) : $_POST) as $key => $val) $_POST[$key] = is_numeric($val) ? (int)$val : $tsCore->setSecure($val);
		// Guardamos
		$SocialName = $tsCore->setSecure($_POST["social_name"]);
		$ClientID = $tsCore->setSecure($_POST["social_client_id"]);
		$ClientSecret = $tsCore->setSecure($_POST["social_client_secret"]);
		$RedirectUri = "{$tsCore->settings['url']}/" . strtolower($SocialName) . ".php";

		if(db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `w_social` (social_name, social_client_id, social_client_secret, social_redirect_uri) VALUES ('$SocialName', '$ClientID', '$ClientSecret', '$RedirectUri')")) return true;
	}

	public function getSocial() {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT social_id, social_name, social_client_id, social_client_secret, social_redirect_uri FROM w_social WHERE social_id = $id"));
		return $data;
	}

	public function saveSocial() {
		global $tsCore;
		$id = isset($_POST['social_id']) ? (int)$_POST['social_id'] : 0;
		$SCI = $tsCore->setSecure($_POST['social_client_id']);
		$SCS = $tsCore->setSecure($_POST['social_client_secret']);
		if(db_exec([__FILE__, __LINE__], 'query', "UPDATE w_social SET social_client_id = '$SCI', social_client_secret = '$SCS' WHERE social_id = $id")) return true;
      return false;
	}

	public function eliminarRed() {
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if($id == 0) return false;
		if(db_exec([__FILE__, __LINE__], 'query', "DELETE FROM w_social WHERE social_id = $id")) {
			return true;
		}
		return false;
	}

	public function desvincular() {
		global $tsCore, $tsUser;
		$red = $tsCore->setSecure($_POST['name']);
		// Que este logueado 
		if($tsUser->is_member) {
			$uid = (int)$tsUser->uid;
			// Buscamos
			$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT u.user_id, u.user_activo, u.user_baneado, u.user_socials, us.social_id, us.social_user_red FROM u_miembros AS u LEFT JOIN u_miembros_social AS us ON us.social_user_id = u.user_id WHERE u.user_id = {$uid} AND us.social_user_red = '$red' LIMIT 1"));
			///
			$social_decode = json_decode($data['user_socials'], true);
			$social_decode[$red] = false;
			$data['user_socials'] = json_encode($social_decode, JSON_FORCE_OBJECT);
			// Actualizamos
			if(db_exec([__FILE__, __LINE__], 'query', "DELETE FROM u_miembros_social WHERE social_user_id = $uid AND social_id = " . (int)$data['social_id'])) {
				db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_socials = '{$data['uer_socials']}' WHERE user_id = " . (int)$uid);
				return true;
			}
			return false;
		}
		
	}

}