<?php

class Places extends Phalcon\Mvc\Model
{
	public function initialize()
    {
        $this->hasMany('id', 'PlaceCategories', 'place_id', array(
            'reusable' => false
        ));
    }
}
