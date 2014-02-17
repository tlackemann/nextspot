<?php

class Tokens extends Phalcon\Mvc\Model
{
    public function initialize()
    {
        // $this->hasMany('user_id', 'Users', 'id', array(
        //     'foreignKey' => array(
        //         'message' => 'Token cannot be deleted because it\'s used on User'
        //     )
        // ));
    }
}
