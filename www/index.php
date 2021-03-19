<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 19/03/2021
 * Time: 01:19
 */

//require_once getcwd() . '/config/db_data.php';
//require_once getcwd() . '/config/bd.php';
require_once getcwd() . '/EnderecoController.php';
require_once getcwd() . '/UsuariosController.php';

if(isset($_GET['m']) && $_GET['m'] == 'enderecos') {
    $endereco = new EnderecosController();
    if(isset($_GET['a']) && $_GET['a'] == 'show') {
        $dados = isset($_REQUEST['dados']) ? $_REQUEST['dados'] : '';
        echo json_encode($endereco->show($dados));
    }
    if(isset($_GET['a']) && $_GET['a'] == 'update') {
        $dados = isset($_POST) ? $_POST : '';
        print_r($_REQUEST);
        exit;
        echo json_encode($endereco->update($dados));
    }

//    print_r($endereco->show());
//    exit;
}


if(isset($_GET['m']) && $_GET['m'] == 'usuarios') {}
