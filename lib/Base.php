<?php

namespace Teste;

use Carbon\Carbon;
use Teste\Config;


/**
 * Base do sistema. Inicializa e configura Twig e flashed messages.
 *
 * @author Fernando Wan-Dall <fernando.wandall@gmail.com>
 */
class Base {


    /**
     * Inicializa configurações do ambiente Twig
     *
     * @var Twig_Loader_Filesystem
     */
    protected static $loader;

    /**
     * Inicializa ambiente Twig
     *
     * @var Twig_Environment
     */
    protected static $Twig;

    /**
     * Valores para serem interpretados pelo Twig no template
     *
     * @var array
     * @see Base::setValue()
     */
    protected static $retorno = [];

    /**
     * Armazena Base para ser reutilizada
     *
     * @var Base
     * @see Base::getInstance
     */
    protected static $instance;

    /**
     * Teste de tempo de execução
     *
     * @internal Debug
     * @todo Remover quando em produção
     *
     * @var integer Tempo inicial da execução 
     */
    private static $tempo_ini;

    /**
     * Teste de tempo de execução
     *
     * @internal Debug
     * @todo Remover quando em produção
     *
     * @var integer Tempo final da execução 
     */
    private static $tempo_fin;


    /**
     * Inicializa ambiente Twig.
     *
     * @param array $templates
     * Caminho dos templates do módulo a ser exibido
     *
     * @see Base::getInstance()
     */
    protected function __construct(array $templates=[]) {
        //Testes desempenho
        //TODO: remover
        self::$tempo_ini = microtime();

        //Caminhos básicos do sistema
        self::$loader = new \Twig_Loader_Filesystem();
        self::$loader->addPath(Config::lnkAb('templates'));

        //Carrega templates de módulos
        foreach ($templates as $namespace => $caminho){
            self::$loader->addPath($caminho, $namespace);
        }

        self::$Twig = new \Twig_Environment(self::$loader, array(
            //Adiciona cache
            //'cache' => Config::lnkAb('cache'),
        ));
        
        /**
         * Pluralize para Twig
         *
         * @param array $array Array para ser analizado
         *
         * @param string $plural Sufixo indicando plural. O padrão é 's'
         */
        self::addTwigFilter('pluralize', function($array, $plural='s'){
                return count($array) > 1 ? $plural : '';
            }
        );

        //transforma strings em data e formata
        self::addTwigFilter('carbonize', function($string, $format='d/m/Y'){
                return Carbon::parse($string)->format($format);
            }
        );

    }


    /**
     * Adiciona filtros personalizados no Twig
     *
     * Exemplo:
     *
     *      'carbonize', function($string, $format='d/m/Y'){
     *              return Carbon::parse($string)->format($format);
     *          }
     *
     * Uso no Twig:
     *
     *      {% set Twig_tag = '20170325 19:39:21' %}
     *      {{ Twig_tag|carbonize('d/m/Y H:i:s') }}
     *
     * @param string $filtro Nome do filtro a ser adicionado no Twig
     * @param function $funcao Corpo da função do filtro
     */
    protected static function addTwigFilter($filtro, $funcao){
        $nFiltro = new \Twig_SimpleFilter($filtro, $funcao);
        self::$Twig->addFilter($nFiltro);
    }


    /**
     * Inicializa Base
     *
     * @param array $templates Adiciona templates para Twig
     *
     *      [namespace => caminho/para/template]
     *
     *      Exemplo:
     *          Base::getInstance(['modulo' => Config::lnkAbMod('Modulo', 'templates')]);
     *
     * @see Config::lnkAb(), Config::lnkAbMod()
     *
     * @return Base
     */
    public static function getInstance(array $templates=[]){
        self::flash('getInstance');
        //die('teste');
        if (!isset(self::$instance)){
            self::$instance = new Base($templates);
        }
        return self::$instance;
    }


    /**
     * Adiciona valores para serem interpretados pelo Twig no template
     *
     * @param array $array Valor a ser adicionado
     *
     *      [Twig_tag => valor]
     */
    public static function setValue(array $array){
        //self::getInstance();
        self::$retorno = array_merge(self::$retorno, $array);
    }


    /**
     * Exibe dados na tela
     *
     * @param string $template Nome do template (com namespace se for módulo)
     *
     *      '@modulo/template.html'
     *
     * @param array $array Array com valores a serem interpretados pelo Twig
     *
     *      [Twig_tag => valor]
     *
     * @see Base::getInstance, Base::_construct
     */
    public static function display($template, array $array = []){
        //Teste
        //TODO: remover
        self::$tempo_fin = microtime();
        self::setValue(['runtime' => self::$tempo_fin - self::$tempo_ini]);

        try {
            //self::getInstance();
            $templ = self::$Twig->loadTemplate($template);

            //Opcao para adicionar array de valores
            if ($array){
                self::$retorno = array_merge(self::$retorno, $array);
            }

            //Flashed
            self::$retorno = array_merge(self::$retorno, ['flashed' => Base::getFlashed()]);

            //Adiciona urls do sistema
            foreach (Config::getLnkArray() as $chave => $valor) {
                self::setValue(['url_' . $chave => $valor]);
            }

            $templ->display(self::$retorno);

        } catch (Exception $e){
            header("HTTP/1.0 404 Not Found", true, 404);
            #header("Location: /404.html");
            die;
        }
    }


    /**
     * Guarda mensagens para serem exibidas no template
     *
     * @param string $mensagem Mensagem a ser exibida
     * @param string $tipo Tipo de alert em que a $mensagem será exibida.
     * Alert são as cores que bootstrap usa para definir cores de fontes e fundos:
     * alert-[danger|default|info|primary|success|warning].
     *
     *      Base::flash('Mensagem', 'danger')
     */
    public static function flash($mensagem, $tipo='info') {
        $_SESSION['flash'][] = ['mensagem' => $mensagem, 'tipo' => $tipo];
    }


    /**
     * Retorna todas as mensagens armazenadas com Base::flash separadas.
     * As mensagens serão agrupadas pelo tipo.
     *
     * @see Base::flash()
     *
     * @return array Array com dados flashed
     */
    public static function getFlashed() {
        if (isset($_SESSION['flash'])){
            foreach ($_SESSION['flash'] as $chave => $valor) {
                $tipo[$chave] = $valor['tipo'];
                $mgs[$chave] = $valor['mensagem'];
            }
            //Ordena array
            array_multisort($tipo, SORT_ASC, $mgs, SORT_ASC, $_SESSION['flash']);
            $msg = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $msg;
        }
    }


    /**
     * Redireciona para destino
     *
     * @param string $destino Chave do destino definida em Config::lnk()
     * @see Config::$urls
     */
    public static function redirect($destino) {
        //echo $destino;
        header("location: " . Config::lnk($destino));
        //Pára processamento
        die;
    }

}


/**
 * Página inicial do site
 *
 * @author Fernando Wan-Dall <fernando.wandall@gmail.com>
 */
class Index extends Base {

    public function __construct() {
        parent::__construct();

        Base::display('index.html', array(
            'header_titulo' => 'Sistema',
        ));
    }
}
