<?php
/**
 * Tela inicial
 * @author Fernando Wan-Dall <fernando.wandall@gmail.com>
 */
session_start();

use Teste\Config;

require_once 'lib/Config.php';
require_once Config::lnkAb('vendor') . 'autoload.php';
require_once Config::lnkAb('lib') . 'Base.php';

/*
 * Nao necessita validacao aqui pois os valores vem diretamente
 * do .htaccess
 */
$require = ucfirst($_GET['require']);
$classe = ucfirst($_GET['class']);

if (file_exists(Config::lnkAb('lib') . $require . '.php')){
    require_once Config::lnkAb('lib') . $require . '.php';
} elseif (file_exists(Config::lnkAb('modulos') . $require . '/View.php')){
    require_once Config::lnkAb('modulos') . $require . '/View.php';
} else {
    print($require . $classe);
    die("Nao achou");
}
//echo $class;
//die(print_r($_GET));
$class = '\\Teste\\' . ($require != 'Base' ? ($require . '\\') : '') . $classe;

if (class_exists($class, FALSE)){
    new $class($_GET, $_POST);
} else {
    print($require . $classe);
    var_dump($class);
    die('Nao achou class');
}
//else {
//    header("HTTP/1.0 404 Not Found", true, 404);
//    header("Location: /estrutura/erros/notfound.html");
//    die;
//    //echo "Classe n&atilde;o encontrada";
//}