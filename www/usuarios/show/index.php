<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 19/03/2021
 * Time: 02:25
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/UsuariosController.php';

$usuarios = new UsuariosController();

$dados = isset($_REQUEST) ? $_REQUEST : '';

echo json_encode($usuarios->show($dados));