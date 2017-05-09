<?php
namespace Teste\Clubes\Forms;

use WTForms\Form;
use WTForms\Fields\Simple\HiddenField;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
use WTForms\Validators\Length;

use Teste\Config;
use Teste\Forms\Validators\OnlyContains;

require Config::lnkAb('validacoes') . 'OnlyContains.php';

/**
 * Formulário de cadastro de Clubes
 */
class CadastroForm extends Form {
    public function __construct(array $options = []) {
        parent::__construct($options);

        $this->nome = new StringField([
            "label" => "Nome do Clube",
            "class" => "form-control",
            "validators" => [
                new InputRequired("Nome do Clube é obrigatório"),
                new Length("Nome do Clube deve ter entre %(min)d e %(max)d caracteres",
                        ['min' => 2, 'max'=> 100]),
                new OnlyContains('Nome do Clube deve conter apenas os caracteres a-z, 0-9, -, _, . e espaços.',
                        ['valid_members' => '/[\w\s\-\.]/u']),
                ],
            ]);

    }
}