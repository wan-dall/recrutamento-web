<?php
namespace Teste;

/**
 * Retorna caminhos para o sistema, desse modo não é necessário preocupar-se
 * com alterações de url ou caminhos
 *
 * @author Fernando Wan-Dall <fernando.wandall@gmail.com>
 */
class Config {

    /**
     * Caminhos base (url e filesystem e módulos)
     *
     * @var array
     */
    protected static $caminhos = [
        'url' => 'http://localhost/recrutamento-web/',
        #'url' => 'http://recrutamento-web.wan-dall.com/',
        'abs' => __DIR__ . '/../',
        #'abs' => '/var/www/html/recrutamento-web/',
        ];

    /**
     * Caminhos absolutos
     *
     *  @var array
     */
    protected static $fls = [
        'root'          => '',
        'cache'         => 'cache/',
        'imagens'       => 'imagens/',
        'lib'           => 'lib/',
        'forms'         => 'lib/Forms/',
        'validacoes'    => 'lib/Forms/validators/',
        'modulos'       => 'modulos/',
        'templates'     => 'templates/',
        'vendor'        => 'vendor/',
        ];

    /**
     * Caminhos internos de módulos
     *
     * @var array
     */
    private static $lnkMod = [
        'forms'         => 'Forms/',
        'validacoes'    => 'Forms/validators/',
        'templates'     => 'templates/',
        ];

    /**
     * Caminhos para links via urls
     *
     * @var array
     */
    protected static $urls = [
        //Basicos
        'root'          => '',
        'css'           => 'static/css/',
        'erros'         => 'static/erros/',
        'imagens'       => 'static/imagens/',
        'js'            => 'static/js/',
        //modulos
        'clubes'        => 'clubes/',
        'socios'        => 'socios/',
        ];


    public static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new Config();
        }
        return self::$instance;
    }
    
    /**
     * Retorna todas as urls em Config::$urls formatadas para uso
     *
     * @return array
     */
    public static function getLnkArray() {
        $lista = [];
        foreach (self::$urls as $chave => $valor){
            $lista += [$chave => self::$caminhos['url'] . $valor];
        }
        return $lista;
    }


    /**
     * Formata url para uso
     *
     *
     * @param string $indice Índice da url utilizada em Config::$urls
     *
     * @return string Url absoluta formatada
     *
     * @see Config::$urls, Config::caminhos
     */
    public static function lnk($indice) {
        echo self::$caminhos['url'] . self::$urls[$indice];
        return self::$caminhos['url'] . self::$urls[$indice];
    }


    /**
     * Formata caminho absoluto
     *
     * @param string $indice Índice do caminho utilizado em Config::$fls
     *
     * @return string Caminho absoluto formatado
     *
     * @see Config::$fls, Config::caminhos
     */
    public static function lnkAb($indice) {
        return self::$caminhos['abs'] . self::$fls[$indice];
    }


    /**
     * Caminhos para conteúdos de módulos
     *
     * @param string $modulo Nome do módulo
     * @param string $indice Índice do conteúdo utilizado em Config::$lnkMod
     *
     * @return string Caminho absoluto formatado
     * 
     * @see Config::$lnkMod, Config::lnkAb()
     */
    public static function lnkAbMod($modulo, $indice) {
        return self::lnkAb('modulos') . $modulo . '/' . self::$lnkMod[$indice];
    }
}
