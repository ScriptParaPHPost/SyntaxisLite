<?php 

require realpath('../../') . DIRECTORY_SEPARATOR . "header.php";

class Callback extends tsCore {

	private $version = '2.0';

	/**
	 * @access public
	 * Puede ser github, discord, google, etc.
	*/
	public $social = '';
	
	public function __construct() {}

	/**
	 * @name getEndPoint
	 * @access public
	 * @param string
	 * @return string
	 * Manejo de OAuth | Token
	*/
	public function getEndPoint(string $type = '') {
		$extract = parent::getEndPoints($this->social, $type);
		return $extract;
	}


	/**
	 * @name buildQuery
	 * @access public
	 * @return string
	 * Control sobre parámetros en cURL
	*/
	public function buildQuery() {
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT social_name, social_client_id, social_client_secret, social_redirect_uri FROM w_social WHERE social_name = '{$this->social}'"));
		$param = [
		 	'client_id' => $data['social_client_id'],
		 	'client_secret' => $data['social_client_secret'],
		 	'grant_type' => 'authorization_code',
		 	'code' => $_GET['code'],
		 	'redirect_uri' => $data['social_redirect_uri']
		];
		// Quitamos 'grant_type'
		if($this->social === 'github' OR $this->social === 'facebook') unset($param['grant_type']);
		// Armamos la consulta
		return http_build_query($param);
	}

	public function httpHeader() {
		if($this->social === 'github') {
			$header = ['Accept: application/json'];
		} elseif($this->social === 'discord') {
			$header = ['Content-Type: application/x-www-form-urlencoded'];
		} 
		return $header;
	}

	public function cURLToken(bool $status = true) {
		$ch = curl_init(self::getEndPoint('token'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, self::buildQuery());
		if($status) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, self::httpHeader());
		}
		$response = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($response);

		return $data;
	}

	private function httpHeaderUser($data) {
		if($this->social === 'github') {
			$array = [
				"Authorization: token " . $data, 
				"User-Agent: ". parent::getSettings()['titulo']
			];
		} elseif($this->social === 'discord' OR $this->social === 'google') {
			$array = ["Authorization: Bearer " . $data];
		}
		return $array;
	}

	public function cURLUser($data) {
		$token = $data->access_token;
		$url_token_user = self::getEndPoint('user') . ($this->social === 'facebook' ? "&access_token={$token}" : '');
		$ch = curl_init($url_token_user);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($this->social !== 'facebook')
			curl_setopt($ch, CURLOPT_HTTPHEADER, self::httpHeaderUser($token));
		$response = curl_exec($ch);
		curl_close($ch);
		$userData = json_decode($response);
		return $userData;
	}

	# ===================================================
	# OAuth
	# Desde acá crearemos, actualizaremos o logueamos
	# ===================================================
	public function OAuthComplete(array $UserData = []) {
		global $tsUser;
		# Verificamos que sea un correo
		if(filter_var($UserData['email'], FILTER_VALIDATE_EMAIL)) {
			# Lo almacenamos en una variable
			$email = parent::setSecure($UserData['email']);
			# Generamos la consulta 
			$usuario = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT u.user_id, u.user_name, u.user_password, u.user_email, u.user_activo, u.user_baneado, u.user_socials, w.social_id, us.social_user_name, us.social_user_email, us.social_user_avatar, us.social_user_red FROM u_miembros AS u LEFT JOIN w_social AS w ON w.social_name = '{$this->social}' LEFT JOIN u_miembros_social AS us ON us.social_user_id = u.user_id WHERE LOWER(user_email) = '$email' OR user_id = {$tsUser->uid} LIMIT 1"));

			# Si no existe el usuario => CREAREMOS
			if(empty($usuario)) self::createNewAccount($UserData);
			# Si existe el usuario y no esta logueado => ACCEDEMOS
			else if(!empty($usuario) AND !$tsUser->is_member) self::accessAccount($usuario);
			# Si existe el usuario y esta logueado => ACTUALIZAREMOS
			else if(!empty($usuario) AND $tsUser->is_member) self::updateAccount($usuario, $UserData);
		} else die('Lo lamento, este '.$UserData['email'].' no es un correo v&aacute;lido.');
	}

	/**
	 * @name miembrosRedes
	 * @access private
	 * @param $userdata
	 * @return boolean
	*/
	private function miembrosRedes(int $id = 0, array $userdata = []):bool {
		$existe = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT social_id FROM u_miembros_social WHERE social_user_id = $id AND social_user_red = '{$this->social}'"));
		if(isset($existe['social_id'])) {
			// Actualizamos datos
			if(db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros_social SET social_user_name = '{$userdata['nick']}', social_user_email = '{$userdata['email']}', social_user_avatar = '{$userdata['avatar']}' WHERE social_user_red = '{$this->social}' AND social_user_id = $id")) return true;
		} else {
			// Insertamos los datos
			if(db_exec([__FILE__, __LINE__], 'query', "INSERT INTO u_miembros_social (social_user_id, social_user_red, social_user_name, social_user_email, social_user_avatar) VALUES($id, '{$this->social}', '{$userdata['nick']}', '{$userdata['email']}', '{$userdata['avatar']}')")) return true;
		}
		return false;
	}

	/**
	 * @name createNewAccount
	 * @access private
	 * @param array
	 * @return redirect
	 * En caso que no exista el usuario, creamos
	 * su cuenta usando su red social elegida
	*/
	private function createNewAccount(array $UserData = []) {
		$rango = empty(parent::getSettings()['c_reg_rango']) ? 3 : (int)parent::getSettings()['c_reg_rango'];
		$active = (int)parent::getSettings()['c_reg_active'];
		$info = [
			'user_name' => $UserData['nick'], 
			'user_email' => $UserData['email'], 
			'user_socials' => json_encode([$this->social => true], JSON_FORCE_OBJECT),
			'user_rango' => $rango, 
			'user_registro' => time(), 
			'user_activo' => $active
		];

		if(db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_miembros` (user_name, user_email, user_socials, user_rango, user_registro, user_activo) VALUES ('{$info['user_name']}', '{$info['user_email']}', '{$info['user_socials']}', $rango, {$info['user_registro']}, $active)")) {
			$id = db_exec('insert_id');
			/**
			 * Creamos avatar
			*/
			$newName = TS_AVATAR . "$id.{$this->social}.webp";
			$img = file_get_contents($userdata['avatar']);
			if ($img === false) echo "Error al cargar la imagen desde la URL.";
			else {
				$img = imagecreatefromstring($img);
			   if ($img === false) echo "Error al crear la imagen desde el contenido descargado.";
			   else {
			       imagepalettetotruecolor($img);
			       imagealphablending($img, true);
			       imagesavealpha($img, true);
			       if (imagewebp($img, $newName, 100)) {
			           // Conversión exitosa, continuar con otras operaciones
			           imagedestroy($img);
			       } else echo "Error al convertir la imagen a WebP.";
			   }
			}
			#####
	     	$sid = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT social_id FROM `w_social` WHERE social_name = '{$this->social}'"))['social_id'];
	     	self::miembrosRedes($id, $UserData);
	     	// INSERTAMOS EL PERFIL
			db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_perfil` (`user_id`, `p_avatar`) VALUES ($id, 1)");
	      db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_portal` (`user_id`) VALUES ($id)");
	      $data = [
	      	'user_id' => $id, 
	      	'user_baneado' => 0,
	      ] + $info;
	      self::accessAccount($data);
		}
	}

	/**
	 * @name accessAccount
	 * @access private
	 * @param array
	 * @return redirect
	 * Si el usuario ya tiene su cuenta vinculada!
	*/
	private function accessAccount(array $user = []) {
		global $tsUser;
		# Usuario activo?
      if((int)$user['user_activo'] === 0) die('Tienes que activar tu cuenta.');
		# Usuario baneado?
      if((int)$user['user_baneado'] === 1) die('Tu has sido baneado.');
		# Esta vinculado?
		$status = json_decode($user['user_socials'], true);
		if($status[$this->social]) {   
			// Actualizamos la session
         $tsUser->sessionUpdate((int)$user['user_id']); 
			/* REDERIGIR */
			parent::redirectTo('./');
		} else die('Tu cuenta no esta vinculada a ' . $this->social);
	}

	/**
	 * @name updateAccount
	 * @access private
	 * @param array
	 * @param array
	 * @return redirect
	 * Si el usuario esta logueado, pero quiere vincular
	 * su cuenta a la red social elegida!
	*/
	private function updateAccount(array $usuario = [], $userdata) {
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_socials FROM u_miembros WHERE user_id = {$usuario['user_id']}"));
		// Lo decodificamos
		if(!empty($data['user_socials'])) $social_decode = json_decode($data['user_socials'], true);
		self::miembrosRedes($usuario['user_id'], $userdata);
		// Lo modificamos o creamos en caso que no exista
		$social_decode[$this->social] = true;
		// Lo armamos otra vez
		$social_encode = json_encode($social_decode, JSON_FORCE_OBJECT);
		// Acutalizamos la tabla
		if(db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_socials = '$social_encode' WHERE user_id = {$usuario['user_id']}")) {
			/* REDERIGIR */
			parent::redirectTo('./');
		}
	}

}

$callback = new Callback;