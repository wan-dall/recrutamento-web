<?php
namespace Teste\Clubes;

use Teste\Base;
use Teste\Config;
use Teste\DBAL;
use Teste\Utils;

use Teste\Clubes\Forms\CadastroForm;

require_once Config::lnkAb('lib') . 'Base.php';
require_once Config::lnkAb('lib') . 'DBAL.php';
require_once Config::lnkAb('lib') . 'Utils.php';

require_once Config::lnkAbMod('Clubes', 'forms') . 'CadastroForm.php';


/**
 * Extende clase Base e adiona templates do módulo
 * 
 * @see Base::getInstance
 */
class Clubes extends Base {

    public function __construct() {
        parent::__construct(['clubes' => Config::lnkAbMod('Clubes', 'templates')]);
    }
}


/**
 * Tela inicial do módulo de clubes
 */
class Index extends Clubes {

    public function __construct() {
        parent::__construct();
        
        $db = DBAL::getInstance();
        
        $qb = $db->queryBuilder();
        $qb->select('id', 'nome')
                ->from('clubes')
                ->orderBy('nome');
        
        $clubes = $db->executeQueryBuilder($qb);

        self::display('@clubes/index.html', array(
            'clubes' => $clubes,
        ));
    }
}


/**
 * Tela de cadastro e edição de Clubes
 */
class Cadastro extends Clubes {

    public function __construct() {
        parent::__construct();

        $form = CadastroForm::create(['formdata' => $_POST]);
        
        $db = DBAL::getInstance();
        
        $qb = $db->queryBuilder();

        if ($_POST){
            
            if (!$form->validate()){
                //Se a validação falhar captura os erros e exibe para o
                //usuário
                foreach ($form->errors as $campo => $erro){
                    foreach ($erro as $e){
                        Base::flash($e, 'danger');
                    }
                }
            } else {
                //Se a validação for positiva redireciona para tela de
                //listagem de clubes e exibe mensagem sobre o sucesso
                //da operação.

                try {
                    $qb->insert('clubes')
                            ->values([
                                'nome' => ':nome',
                            ]);

                    //Executa QueryBuilder passando campos para processamento
                    //pelo DBAL
                    //@see Utils::preparaForm
                    $db->executeQueryBuilder($qb, Utils::preparaForm($form));

                    Base::flash(sprintf("Clube '%s' cadastrado com sucesso", $form->nome->data), 'success');
                    Base::redirect('clubes');

                } catch (Exception $ex) {
                    Base::flash('Erro ao tentar salvar clube: ' . $ex);
                }
            }

        }

        self::display('@clubes/cadastro.html', array(
            'form' => $form,
        ));
    }
}

