<?php
class Principal extends ControllerBase
{
    function __construct()
    {
        parent::__construct();
    }

    function render(){
        $this->view->render("principal/index");
    }
}

?>