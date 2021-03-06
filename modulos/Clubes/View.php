<?php
namespace Teste\Clubes;

use \Doctrine\DBAL\Exception\UniqueConstraintViolationException;

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

                } catch (UniqueConstraintViolationException $e) {
                    Base::flash("Nome de Clube '" . $form->nome->data . "' já em uso", 'danger');
                } catch (\Exception $e){
                    Base::flash("Aconteceu um erro ao tentar salvar os dados. Por favor tente novamente em alguns segundos", 'danger');
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
                Base::flash('Clube não encontrado', 'warning');
                Base::redirect('clubes');
            }
        }

        self::display('@clubes/cadastro.html', array(
            'form' => $form,
            'clube' => $clube,
        ));
    }
}


/**
 * Remove clube via ajax;
 */
class Remove extends Clubes {

    public function __construct() {
        parent::__construct();

        //Valida id passado
        if (isset($_POST['id']) && is_numeric($_POST['id'])){
            $id = $_POST['id'];

            try {
                $db = DBAL::getInstance();

                $qb = $db->queryBuilder();
                $expr = $qb->expr();
                //Remove Clube
                $qb->delete('clubes')
                        ->where($expr->eq('id', ':id'));

                $db->executeQueryBuilder($qb, ['id' => $id]);

                //Limpa ids do clube removido de sócios
                $qb->update('socios')
                        ->set('clube_id', $expr->literal(''))
                        ->where($expr->eq('clube_id', ':id'));

                $db->executeQueryBuilder($qb, ['id' => $id]);
                Base::flash('Clube removido', 'success');

            } catch (\Exception $e){
                //$mensagem = 'nok' . $e->getMessage();
                Base::flash('Erro ao tentar remover Clube. Por favor tente novamente em alguns segundos', 'danger');
            }
        }
    }
}
