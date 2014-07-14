<?php

class Categories extends Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->belongsTo('parent_id', 'Categories', 'id');
    }
}
