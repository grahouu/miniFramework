<?php
function get_path_info() {
  if (!array_key_exists('PATH_INFO', $_SERVER)) {
    if (!$_SERVER['QUERY_STRING'])
      return "";
    $pos = strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);

    $asd = substr($_SERVER['REQUEST_URI'], 0, $pos - 2);
    $asd = substr($asd, strlen($_SERVER['SCRIPT_NAME']) + 1);

    return $asd;
  } else {
    return trim($_SERVER['PATH_INFO'], '/');
  }
}

global $connect;
function db_open_connexion() {
  return pg_connect("host=localhost port=5432 dbname=controle_serveur user=postgres password=mot_de_passe"); //TODO: Changer le port, utilisateur et mot de passe en prod
}

function db_close_connexion($connect) {
  pg_close($connect);
}

//Date au format PostGres SQL PHP
function date_to_timestamp($time) {
  return date('Y-m-d H:i:s.u', $time);
}

//Timestamp postgresSQL au format lisible
function timestamp_to_date($timestamp, $format = "dateheure") {
  $time = strtotime($timestamp);

  //Applique le fuseau horaire de la France
  date_default_timezone_set('Europe/Paris');
  $date = date('Y-m-d H:i:s.u', $time);
  date_default_timezone_set('UTC');

  $timestampunix = strtotime($date);
  if ($format == "date")
    return date('d/m/Y', $timestampunix);
  else
    return 'Le ' . date('d/m/Y', $timestampunix) . ' &agrave; ' . date('H:i:s', $timestampunix);
}

// fonction pour analyser l'en-tête http auth
function http_digest_parse($txt) {
  // protection contre les données manquantes
  $needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
  $data = array();
  $keys = implode('|', array_keys($needed_parts));

  preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

  foreach ($matches as $m) {
    $data[$m[1]] = $m[3] ? $m[3] : $m[4];
    unset($needed_parts[$m[1]]);
  }

  return $needed_parts ? false : $data;
}

function current_user() {
  if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
    global $password, $realm;
    $current_user = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
    // Génération de réponse valide
    $A1 = md5($current_user['username'] . ':' . $realm . ':' . $password);
    $A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $current_user['uri']);
    $valid_response = md5($A1 . ':' . $current_user['nonce'] . ':' . $current_user['nc'] . ':' . $current_user['cnonce'] . ':' . $current_user['qop'] . ':' . $A2);

    if ($current_user['response'] != $valid_response) {
      $current_user = null;
    }
  } else {
    $current_user = null;
  }
  return $current_user;
}
?>
