<?php

/**
 * Autor: Miguel92
 * Ejemplo: getOpenGraph::getUrlDataInfo('url', ['title', 'description', 'image']);
 * Enlace: Enlace 
 * Fecha: 24.01.24 
 * Nombre: getOpenGraph
 * Proposito: Obtener datos como titulo, descripcion e imagen 
 * Version: v2.0 
*/

class getOpenGraph {

   private static function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0) {
      $result = false;
      $contents = @file_get_contents($url);
      if (isset($contents) && is_string($contents)) {
         preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
         if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1) {
            if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections) return self::getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
            $result = false;
         } else $result = $contents;
      }
      return $contents;
   }
   
   public static function getUrlData($url, $raw=false) {
      $result = false;
      $contents = self::getUrlContents($url);
      if (isset($contents) && is_string($contents)) {
         $title = null;
         $metaTags = null;
         $metaProperties = null;
         #
         preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
         #
         if (isset($match) && is_array($match) && count($match) > 0) $title = strip_tags($match[1]);
         #
         preg_match_all('/<[\s]*meta[\s]*(name|property)="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
         #
         if (isset($match) && is_array($match) && count($match) == 4) {
            $originals = $match[0];
            $names = $match[2];
            $values = $match[3];
            if (count($originals) == count($names) && count($names) == count($values)) {
               $metaTags = array();
               $metaProperties = $metaTags;
               if ($raw) {
                  $flags = (version_compare(PHP_VERSION, '5.4.0') == -1) ? ENT_COMPAT : ENT_COMPAT | ENT_HTML401;
                  for ($i=0, $limiti=count($names); $i < $limiti; $i++) {
                     $meta_type = ($match[1][$i] == 'name') ? 'metaTags' : 'metaProperties';
                     if ($raw) {
                        $$meta_type[$names[$i]] = [
                           'html' => htmlentities($originals[$i], $flags, 'UTF-8'),
                           'value' => $values[$i]
                        ];
                     } else {
                        $$meta_type[$names[$i]] = [
                           'html' => $originals[$i],
                           'value' => $values[$i]
                        ];
                     }
                  }
                  # for
               }
               # if raw
            }
            $result = array (
               'title' => $title,
               'metaTags' => $metaTags,
               'metaProperties' => $metaProperties,
            );
         }
         return $result;
      }
   }

   private static function checkImage(array $data = []) {
      if(isset($data["metaTags"]['og:image'])) {
         $image = $data["metaTags"]["og:image"]["value"];
      } elseif(isset($data["metaProperties"]['og:image'])) {
         $image = $data["metaProperties"]["og:image"]["value"];
      } else {
         $image = $tsCore->settings['url'] . "/files/portada.png";
      }
      return $image;
   }

   public static function getUrlDataInfo(string $url = '', array $datos = []) {
      $obtener_datos = self::getUrlData($url, true);
      $datos = [
         'title' => $obtener_datos["title"],
         'description' => $obtener_datos["metaTags"]["description"]["value"]
      ];
      $datos['image'] = self::checkImage($obtener_datos);
      return $datos;
   }
}