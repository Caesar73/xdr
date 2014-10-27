<?php

class StaticAction extends Action {

    public function login () {
        $this -> display('login.html');
    }

    public function loginAjax () {
        $data = array(
            'name' => 'zhangjiayi'
        );

        $this -> assign('data', $data);
        //echo json_encode($data);
        //
        $content = $this -> fetch('loginAjax.html', false);
        echo $content;
    }

    public function test () {
        echo 'test page';
    }

    public function index () {
        echo 'index page';
    }

    public function home () {
        echo 'home page';
    }
}