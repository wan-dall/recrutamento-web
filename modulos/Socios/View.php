<?php
namespace Teste\Socios;

use Teste\Base;
use Teste\Config;
use Teste\DBAL;
use Teste\Utils;

use Teste\Socios\Forms\CadastroForm;

require_once Config::lnkAb('lib') . 'Base.php';
require_once Config::lnkAb('lib') . 'DBAL.php';
require_once Config::lnkAb('lib') . 'Utils.php';

require_once Config::lnkAbMod('Socios', 'forms') . 'CadastroForm.php';


/**
 * Extende clase Base e adiona templates do módulo
 * 
 * @see Base::getInstance
 */
class Socios extends Base {

    public function __construct() {
        parent::__construct(['socios' => Config::lnkAbMod('Socios', 'templates')]);
    }
}


/**
 * Tela inicial do módulo de sócios
 */
class Index extends Socios {

    public function __construct() {
        parent::__construct();

        $db = DBAL::getInstance();

        $qb = $db->queryBuilder();
        $qb->select('id', 'nome')
                ->from('clubes')
                ->orderBy('nome');

        $clubes = $db->executeQueryBuilder($qb);

        self::display('@socios/index.html', array(
            'clubes' => $clubes,
        ));
    }
}


/**
 * Tela de cadastro e edição de Socios
 */
class Cadastro extends Socios {

    public function __construct() {
        parent::__construct();

        $form = CadastroForm::create(['formdata' => $_POST]);

        $db = DBAL::getInstance();
        $qb = $db->queryBuilder();

        //Captura id do clube
        $id = False;
        if (isset($_GET['val1'])){
            $id = $_GET['val1'];
        }

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

                //Parâmetros preparado para query
                $params = Utils::preparaForm($form);

                try {

                    if ($id){
                        //Se houver id atualiza clube selecionado
                        $expr = $qb->expr();
                        $qb->update('clubes')
                            ->set('nome', ':nome')
                            ->where($expr->eq('id', ':id'));

                        //Adiciona id nos parâmetros para query
                        $params += ['id' => $id];
                    } else {
                        $qb->insert('clubes')
                            ->values(['nome' => ':nome']);
                    }

                    //Executa QueryBuilder passando campos para processamento
                    //pelo DBAL
                    //@see Utils::preparaForm
                    $db->executeQueryBuilder($qb, $params);

                    Base::flash(sprintf("Clube '%s' %s com sucesso", $form->nome->data, ($id ? 'alterado' : 'cadastrado')), 'success');
                    Base::redirect('clubes');

                } catch (Exception $ex) {
                    Base::flash('Erro ao tentar salvar clube: ' . $ex);
                }
            }

        }

        //Pega dados de clube se visualizando
        $clube = False;
        if ($id){
            $expr = $qb->expr();
            $qb->select('nome')
                    ->from('clubes')
                    ->where($expr->eq('id', ':id'));

            $clube = $db->executeQueryBuilder($qb, ['id' => $id])->fetch();

            //Se existir cadastro preenche o Form
            if ($clube){
                $form = Utils::PreencheForm($form, $clube);
            } else {
                Base::raise();
            }
        }

        self::display('@socios/cadastro.html', array(
            'form' => $form,
            'clube' => $clube,
        ));
    }
}
