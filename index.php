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


$require = ucfirst($_GET['require']);
$classe = ucfirst($_GET['class']);


if (file_exists(Config::lnkAb('modulos') . $require . '/View.php')){
    require_once Config::lnkAb('modulos') . $require . '/View.php';
} else {
    header("Location: " . Config::lnk('erros') . "notfound.html");
}

$class = '\\Teste\\' . ($require != 'Base' ? ($require . '\\') : '') . $classe;

if (class_exists($class, FALSE)){
    new $class($_GET, $_POST);
} else {
    #header("HTTP/1.0 404 Not Found", true, 404);
    header("Location: " . Config::lnk('erros') . "notfound.html");
}