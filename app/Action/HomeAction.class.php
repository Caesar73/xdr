<?php

class HomeAction extends Action {

    public function index () {
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
}