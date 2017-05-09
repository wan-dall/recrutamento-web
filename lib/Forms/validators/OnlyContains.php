<?php
namespace Teste\Forms\Validators;

use WTForms\Validators\Validator;
use WTForms\Form;
use WTForms\Fields\Core\Field;
use WTForms\Exceptions\ValidationError;

class OnlyContains extends Validator
{
    /**
     * @var string|array
     */
    public $valid_members;

    /**
     * @param string $message Error message to raise in case of a validation error
     * @param array  $options
     */
    public function __construct($message = "", array $options = ['valid_members' => []]){
        assert(!empty($options['valid_members']), "Message");
        $this->valid_members = $options['valid_members'];
        $this->message = $message;
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @return mixed True if the field passed validation, a Validation Error if otherwise
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = ""){
        //die(strlen(preg_replace($this->valid_members, "", $field->data)));
        if (strlen(preg_replace($this->valid_members, "", $field->data)) > 0) {
            if ($message == "") {
                if ($this->message == "") {
                    $message = "Invalid Input.";
                } else {
                    $message = $this->message;
                }
            }
            throw new ValidationError($message);
        }

        return true;
    }
}