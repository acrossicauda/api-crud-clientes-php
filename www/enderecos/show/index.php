<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 19/03/2021
 * Time: 02:25
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/EnderecoController.php';

$endereco = new EnderecosController();
$dados = isset($_REQUEST['dados']) ? $_REQUEST['dados'] : '';
echo json_encode($endereco->show($dados));

