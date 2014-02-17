<?php

class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('splash');
        Phalcon\Tag::setTitle('Discover the hottest places by you');
        parent::initialize();
    }

    public function indexAction()
    {
        if ($this->session->get('auth'))
        {
            $this->forward('users/index');
        }
        // if (!$this->request->isPost()) {
        //     $this->flash->notice('This is a sample application of the Phalcon PHP Framework.
        //         Please don\'t provide us any personal information. Thanks');
        // }
    }

    public function backgroundAction()
    {
        $this->view->disable();

        $bgs = array(
            'bigstock-At-The-Bar-6556174.jpg',
            'bigstock-Bar-3245424.jpg',
            'bigstock-Group-of-party-people--men-an-41762209.jpg',
            'bigstock-Mans-hand-pouring-pint-of-beer-50551841.jpg',
            'bigstock-Tilted-composition-of-red-cock-28960154.jpg'
        );

        $randIndex = rand(0, count($bgs)-1);

        echo json_encode(array('image' => $bgs[$randIndex]));
    }
}
