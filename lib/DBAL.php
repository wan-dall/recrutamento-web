<?php
namespace Teste;

use Teste\Config;

require Config::lnkAb('lib') . 'Utils.php';

/**
 * Abstração de banco de dados usando Doctrine DBAL
 * 
 * Pode ser usado com vários acessos a diferentes tipos de DBs simultaneamente
 *
 * Testado com MSSQL, MySQL, PostgreSQL.
 *
 * @author Fernando Wan-Dall <fernando.wandall@gmail.com>
 */
class DBAL {
    
    /**
     * Armazena instâncias de bancos de dados
     *
     * @var array
     */
    private static $instance = [];

    /**
     * @var type \Doctrine\DBAL\DriverManager::getConnection
     */
    private $Conn;
    
    private $erro,
            $erroMsg;


    private function __construct($db){
        /**
         * Declara valores básicos para diferentes vendors para configurar 
         * conexção
         */
        switch ($db){
            case 'MySQL':
                //Base::flash('MySQL', 'success');
                $conParams = array(
                    'driver' => 'pdo_mysql',
                    'host' => db,
                    'user' => 'recrutamento_web',
                    'password' => 'pass_web',
                    'dbname' => 'recrutamento-web',
                    );
                break;

            default:
                die('Banco não selecionado');
        }
        
        /**
        * Adiciona camada de portabilidade entre diferente vendors
        */
       $conParams += [
           'wrapperClass' => 'Doctrine\DBAL\Portability\Connection',
           'portability' => \Doctrine\DBAL\Portability\Connection::PORTABILITY_ALL,
           'fetch_case' => \PDO::CASE_LOWER,];

        try {
            $config = new \Doctrine\DBAL\Configuration();

            $this->Conn = \Doctrine\DBAL\DriverManager::getConnection($conParams, $config);

        } catch (PDOException $e){
            print("Erro acesso: " . $e);
            die($e->getMessage());
        }
    }


    /**
     * Inicializa novo acesso à um banco de dados
     *
     * @param string $db Identificação do banco desejado (MySQL, PgSQL, MSSQL)
     *
     * @see DBAL::__construct()
     *
     * @return DBAL
     */
    public static function getInstance($db='MySQL'){
        if (!isset(self::$instance[$db])){
            self::$instance[$db] = new DBAL($db);
        }
        return self::$instance[$db];
    }


    /**
     * Inicializa Doctrine QueryBuilder
     * 
     * @return Doctrine\DBAL\Query\QueryBuilder
     */
    public function querybuilder() {
        return $this->Conn->createQueryBuilder();
    }


    /**
     * Executa query builder
     *
     * @param DBAL::queryBuilder $qb QueryBuilder
     * @param array $params Argumentos e valores para serem incluídos na query
     *
     *      ['valor' => 'Valor']
     *
     * @return mixed Resultado
     */
    public function executeQueryBuilder($qb, $params=[]) {
        if (count($params)){
            $qb->setParameters($params);
        }
        return $qb->execute();
    }

}
