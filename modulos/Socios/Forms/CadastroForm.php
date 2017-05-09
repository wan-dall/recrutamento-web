<?php
namespace Teste\Socios\Forms;

use WTForms\Form;
use WTForms\Fields\Simple\HiddenField;
use WTForms\Fields\Core\StringField;
use WTForms\Fields\Core\SelectField;
use WTForms\Validators\InputRequired;
use WTForms\Validators\Length;

use Teste\Config;
use Teste\DBAL;
use Teste\Forms\Validators\OnlyContains;

require_once Config::lnkAb('lib') . 'DBAL.php';
require Config::lnkAb('validacoes') . 'OnlyContains.php';

/**
 * Formulário de cadastro de Sócios
 */
class CadastroForm extends Form {
    public function __construct(array $options = []) {
        parent::__construct($options);

        $this->nome = new StringField([
            "label" => "Nome Completo",
            "class" => "form-control",
            "validators" => [
                new InputRequired("Nome é obrigatório"),
                new Length("Nome deve ter entre %(min)d e %(max)d caracteres",
                        ['min' => 2, 'max'=> 150]),
                new OnlyContains('Nome deve conter apenas os caracteres a-z, 0-9, -, _, . e espaços.',
                        ['valid_members' => '/[\w\s\-\.]/']),
                ],
            ]);

        //Pega lista de grupos cadastrados;
        $db = DBAL::getInstance();
        $qb = $db->queryBuilder();
        $qb->select('id', 'nome')
                ->from('clubes')
                ->orderBy('nome');
        $clubes = $db->executeQueryBuilder($qb);

        $lista_clubes = [];
        while ($clube = $clubes->fetch()){
            $lista_clubes[] = [$clube['id'], $clube['nome']];
        }

        $this->clube_id = new SelectField([
            "label" => "Clube",
            "class" => "form-control",
            "choices" => $lista_clubes,
            "validators" => [
                new InputRequired("Clube é obrigatório"),
                ]
        ]);

    }
}