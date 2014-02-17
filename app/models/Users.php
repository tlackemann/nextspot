<?php

use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;

class Users extends Phalcon\Mvc\Model
{
    public function validation()
    {
        return true;
        // $this->validate(new EmailValidator(array(
        //     'field' => 'email'
        // )));
        // $this->validate(new UniquenessValidator(array(
        //     'field' => 'email',
        //     'message' => 'Sorry, The email was registered by another user'
        // )));
        // $this->validate(new UniquenessValidator(array(
        //     'field' => 'username',
        //     'message' => 'Sorry, That username is already taken'
        // )));
        // if ($this->validationHasFailed() == true) {
        //     return false;
        // }
    }
    public function initialize()
    {
        $this->hasMany('id', 'Tokens', 'user_id', array(
            'reusable' => false
        ));

        // $this->hasMany('user_id', 'Tokens', 'id', array(
        //     'foreignKey' => array(
        //         'message' => 'Token cannot be deleted because it\'s used on User'
        //     )
        // ));
    }
}
