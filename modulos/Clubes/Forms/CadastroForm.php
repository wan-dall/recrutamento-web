<?php
namespace Teste\Clubes\Forms;

use WTForms\Form;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
use WTForms\Validators\Length;

use \Teste\Config;
use Teste\Forms\Validators\OnlyContains;

require Config::lnkAb('validacoes') . 'OnlyContains.php';

/**
 * Formulário de cadastro de Clubes
 */
class CadastroForm extends Form {
    public function __construct(array $options = []) {
        parent::__construct($options);

        $this->nome = new StringField([
            "label" => "Título",
            "class" => "form-control",
            "validators" => [
                new InputRequired("Título é obrigatório"),
                new Length("Título deve ter entre %(min)d e %(max)d caracteres",
                        ['min' => 2, 'max'=> 100]),
                new OnlyContains('Título deve conter apenas os caracteres a-z, 0-9, -, _, . e espaços.',
                        ['valid_members' => '/[\w\s\-\.]/']),
                ],
            ]);

    }
}