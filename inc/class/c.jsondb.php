<?php if ( ! defined('TS_HEADER')) exit('No se permite el acceso directo al script');
/**
 * @name    c.jsondb.php
 * @author Miguel92
 * @copyright 2020-2024
*/

class tsJson {
	
	private $style = '';

	private $fuentes = [
		"unplash" => "https://source.unsplash.com/IMAGEID",
		"pexels" => "https://images.pexels.com/photos/IMAGEID/pexels-photo-IMAGEID.jpeg"
 	];

	/** 
	 * @access public 
	 * @name getContentJson
	 * @use Obtener la infomación del json y creamos si no existe
	 * @param string
	 * @param int
	 * @return object => stdClass
	*/
	public function getContentJson(string $type = '', int $id = 0) {
		$filename_base = TS_SETTINGS . "config-example-$type.json";
		$filename_copy = TS_SETTINGS . ($id === 0 ? "settings.json" : "config-$id.json");
		// Consultamos si existe
		if(!file_exists($filename_copy)) {
			// Si no existe, lo crearemos
			if (!copy($filename_base, $filename_copy)) echo "Error al copiar $filename_copy...\n";
		}
		$jsonDecode = json_decode(file_get_contents($filename_copy));
		return $jsonDecode;
	}

	/** 
	 * @access private 
	 * @name filePut
	 * @use Obtenemos información desde URL y la guardamos localmente
	 * @param string
	 * @param string
	 * @return string
	*/
	private function filePut(string $rutauno = '', string $rutados = '', array $tamano = []) {
		$datos = file_get_contents($rutados);
		if ($datos !== false) {
     		// Crear una imagen desde los datos JPEG
        	$imagen = imagecreatefromstring($datos);
        	if ($imagen !== false) {
        		// Recortar la imagen según los parámetros proporcionados
            $imagenRecortada = imagecrop($imagen, [
            	'x' => 0, 
            	'y' => 0, 
            	'width' => $tamano['w'], 
            	'height' => $tamano['h']
            ]);
        		// Guardar la imagen en formato WebP con la calidad deseada
            imagewebp($imagen, $rutauno, 80);
            // Liberar la memoria ocupada por la imagen
            imagedestroy($imagen);
            imagedestroy($imagenRecortada);
        	} else echo "Error al crear la imagen desde los datos JPEG.\n";
		} else echo "Error al leer los datos de $rutados...\n";
	}

	private function getIDImage(string $site = '', string $ruta = '') {
		$imagen = pathinfo($ruta)['filename'];
		return str_replace($site, '', $imagen);
	}
	/** 
	 * @access public 
	 * @name generateImage
	 * @use Con esta función hacemos verificaciones para generar
	 *      nuevas imagenes para portada/background(header)
	 * @param string
	 * @param string
	 * @param object => stdClass
	 * @return string
	*/
	public function generateImage(string $nombre = '', string $ruta = '', $json = null) {
		$tsCore = new tsCore;
		$tsUser = new tsUser;
		if ($json instanceof stdClass) {
			$cover = isset($json->portada) ? 'portada' : 'background';
			$pagina = $json->$cover->type;
			$url = str_replace('IMAGEID', $json->$cover->id, $this->fuentes[$pagina]);
			if($pagina == 'pexels') {
				# Seleccionamos los datos de pexels
				$param = $json->params;
				$url .= "?" . http_build_query([
					'h' => $json->$cover->height,
					'w' => $json->$cover->width,
					'auto' => $param->auto,
					'cs' => $param->cs,
					'dpr' => $param->dpr,
					'fit' => $param->fit
				]);
			}
			$sizes = [
				'h' => $json->$cover->height,
				'w' => $json->$cover->width
			];
			if($pagina === 'unplash') $url .= "/{$json->$cover->width}x{$json->$cover->height}";
			if(!$tsUser->is_member) {
				// Actualizar la URL que se devuelve
				$response = "{$tsCore->settings['downloads']}/$nombre";
			} 
			// Existe la imagen
			if(file_exists($ruta)) {
				# No existe entoces, la descargaremos y solo si es 1er usuario o admin
				if((int)$tsUser->is_admod OR (int)$tsUser->uid === 1) {
					$idimag = self::getIDImage($pagina, $ruta);
					// Si las id's no son iguales
					if($json->$cover->id !== $idimag) {
						// Y descargaremos la portada otra vez
						self::filePut($ruta, $url, $sizes);
						// Actualizar la URL que se devuelve
						$response = "{$tsCore->settings['downloads']}/$nombre?" . time();
					// Si son iguales, simplemente devolvemos la url
					} else $response = "{$tsCore->settings['downloads']}/$nombre?" . time();
				} else $response = "{$tsCore->settings['downloads']}/$nombre";
			} else {
				// Descargar y reemplazar la portada
				self::filePut($ruta, $url, $sizes);
				// Actualizar la URL que se devuelve
				$response = "{$tsCore->settings['downloads']}/$nombre?" . time();
			} 
			return $response;
		} else {
		  // Manejar el caso en el que el objeto JSON no es válido
		  echo "El objeto JSON no es válido.";
		  return;
		}
	}

	/** 
	 * @access public 
	 * @name getAdminInfo
	 * @use Extraemos toda la información del archivo ADMIN
	 * @return array
	*/
	public function getAdminInfo():array {
		# Buscamos el archivo
		$json = self::getContentJson('settings');
		# Asignamos los valores
		foreach ($json->background as $k => $ty) {
			if(in_array($json->background, ['type', 'id'])) continue;
			$data[$k] = $json->background->$k;
		}
		# Que página usa?
		$data += [
			'web' => $json->background->type,
			'id' => $json->background->id
		];
		# Retornamos
		return $data;
	}

	/** 
	 * @access public 
	 * @name getAddInfo
	 * @use Agregamos la información a la página para renderizarlo
	 * @return array
	*/
	public function getAddInfo() {
		# Configuraciones del usuario
		$json = self::getContentJson('settings');
		# Tipo
		$type = $json->background->type;
		# Que página?
		$data['web'] = $type;
		$data['id'] = $json->background->id;
		# Creamos la ruta de la imagen
		// Nombre del archivo de la portada actual
		$backgroundname = "$type{$data['id']}.webp";
		$backgroundroute = TS_DOWNLOADS . $backgroundname;

		foreach ($json->background as $k => $y) {
			if(in_array($k, ['type', 'id', 'width', 'height', 'img'])) continue;
			$style .= "background-$k: {$json->background->$k}!important;";
		}
		# Para retornar datos armados
		$data['url'] = self::generateImage($backgroundname, $backgroundroute, $json);
		$data['css'] = $style;
		# Retornamos
		return $data;
	}

	/** 
	 * @access public 
	 * @name save_json
	 * @use Guardamos todo el el archivo 
	 * @param array
	 * @return array
	*/
	public function save_json(string $param = '') {
		# Buscamos el archivo
  		$admin = (array)self::getContentJson('settings');
  		$_POST['robots'] = isset($_POST['robots']) ? $_POST['robots'] : 0;
  		foreach ($_POST as $k => $newValue) {
  			if(is_array($newValue)) {
  				foreach($newValue as $i => $val) $newData[$k][$i] = is_numeric($val) ? (int)$val : $val;
  			} else $newData[$k] = is_numeric($newValue) ? (int)$newValue : $newValue;
  		}
		$admin[$param] = $newData;
		# Insertamos los datos al archivo json, y retornamos
		$rsp = (file_put_contents(TS_SETTINGS . "settings.json", json_encode($admin, JSON_PRETTY_PRINT))) ? '1: Los cambios fuerón aplicados!' :  '0: Los datos no se guardaron.';
		return $rsp;
	}

	/** 
	 * @access public 
	 * @name save_mode
	 * @use Guardamos el tipo dar/light
	 * @return array
	*/
	public function save_mode() {
		$file = isset($_POST['uid']) ? 'config' : 'settings';
		# Buscamos el archivo
  		$admin = (array)self::getContentJson($file, (int)$_POST['uid']);
  		$admin['mode'] = isset($_POST['mode']) ? $_POST['mode'] : 'light';
		# Insertamos los datos al archivo json, y retornamos
		$in = isset($_POST['uid']) ? $file.'-'.(int)$_POST['uid'] : $file; 
		$rsp = (file_put_contents(TS_SETTINGS . "$in.json", json_encode($admin, JSON_PRETTY_PRINT))) ? '1: Los cambios fuerón aplicados!' :  '0: Los datos no se guardaron.';
		return $rsp;
	}

	/** 
	 * @access public 
	 * @name getMode
	 * @use Obtenemos el modo dark/light
	 * @return array
	*/
	public function getMode() {
		$tsUser = new tsUser;
		$file = ($tsUser->is_member) ? 'config' : 'settings';
		$uid = ($tsUser->is_member) ? (int)$tsUser->uid : 0;
		# Retornamos
		return self::getContentJson($file, $uid)->mode;
	}

	/** 
	 * @access public 
	 * @name getSeo
	 * @use Obtenemos los datos para el seo
	 * @return array
	*/
	public function getSeo() {
		$tsCore = new tsCore;
		# Buscamos el archivo
		$json = (array)self::getContentJson('settings')->seo;
		# Recorremos
		foreach($json as $key => $val) {
			if($val instanceof stdClass) {
				foreach ((array)$val as $i => $valores) {
					if(is_numeric($i)) $data['images'][$i] = $valores;
					else $data['robots_data'][$i] = $valores;
				}
			} else $data[$key] = $val;
		}
		$data['seo_images_total'] = [16, 32, 64];
		# Retornamos
		return $data;
	}
	
	### CONFIGURACIONES PARA EL USUARIOS NORMAL ###

	/** 
	 * @access public 
	 * @name getAddInfo
	 * @use Extraemos toda la información del archivo USER
	 * @return array
	*/
	public function getInfoPortada(){
		$tsUser = new tsUser;
		$json = self::getContentJson('user', $tsUser->uid);
		# data
		$response = [
			'position' => $json->portada->position,
			'repeat' => $json->portada->repeat,
			'width' => $json->portada->width,
			'height' => $json->portada->height,
			'id_img' => $json->portada->id,
			'web' => $json->portada->type
		];
		# Retornamos
		return $response;
	}

	/** 
	 * @access public 
	 * @name getAddInfo
	 * @use Guardamos todo el el archivo
	 * @return array
	*/
	public function saveInfoPortada() {
		$tsCore = new tsCore;
		$tsUser = new tsUser;
		$id = (int)$_POST['pid'];
	
		if($tsUser->uid == $id) {
			$sql = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_id FROM u_miembros WHERE user_id = {$id}"));
			# Ahora lo agregaremos al achivo
			$file_user = TS_SETTINGS . "config-{$sql['user_id']}.json";
			$j = [];
			if ( file_exists($file_user) ){
				$j = (array)json_decode(file_get_contents($file_user));
			} else {
				# Si no existe creamos uno! No preguntamos.
				$fichero = TS_SETTINGS . "config-example-user.json";
				$nuevo_fichero = TS_SETTINGS . "config-{$sql['user_id']}.json";
				if (!copy($fichero, $nuevo_fichero)) echo "Error al copiar $fichero...\n";
			}
			# Añadimos los nuevos datos
			$data = [
				'height' => (int)$_POST['height'],
				'id' => $tsCore->setSecure($_POST['imagen']),
				'position' => $tsCore->setSecure($_POST['position']),
				'repeat' => $tsCore->setSecure($_POST['repeat']),
				'type' => $tsCore->setSecure($_POST['sitio']),
				'width' => (int)$_POST['width']
			];
			$j['portada'] = $data;
			# Insertamos los datos al archivo json
			file_put_contents($file_user, json_encode($j, JSON_PRETTY_PRINT));
			//
			$json = self::getContentJson('user', $id);
			foreach ($data as $k => $prop) {
				if(in_array($k, ['type', 'id', 'width', 'height'])) continue;
				$res['css']["background".ucfirst($k)] = $prop;
			}
			# Para retornar datos armados
			$backgroundname = "{$_POST['sitio']}{$json->portada->id}-$id.jpg";
			$backgroundroute = TS_DOWNLOADS . $backgroundname;
			unlink($backgroundroute);
			$res['url'] = self::generateImage($backgroundname, $backgroundroute, $json);

			return json_encode(['msg' => '1: Los cambios fueron aplicados!', 'obj' => $res]);
		} else return json_encode(['msg' => '0: Tu ID no es válido.']);
	}
	# Fin del la clase
}