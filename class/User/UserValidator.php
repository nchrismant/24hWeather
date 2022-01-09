<?php
namespace Météo\User;

use Météo\Connection;
use Météo\Validator;
use PDO;

class UserValidator extends Validator {

    public function validates(array $data) {
        $pdo = Connection::getPDO();
        parent::validates($data);
        $this->validate('newmail', 'isMail');
        $this->validate('newmail', 'existMail', new UserTable($pdo));
        $this->validate('newuser', 'existUser', new UserTable($pdo));
        return $this->errors;
    }
}
?>