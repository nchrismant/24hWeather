<?php
namespace Météo\Calendar;

use Météo\Validator;

class EventValidator extends Validator {

    public function validates(array $data) {
        parent::validates($data);
        $this->validate('name', 'minLength', '3');
        $this->validate('date', 'date');
        $this->validate('start', 'beforeTime', 'end');
        return $this->errors;
    }
}
?>