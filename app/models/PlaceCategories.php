<?php

class PlaceCategories extends Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->hasMany('category_id', 'Categories', 'id', array(
            'reusable' => false
        ));
    }
}
