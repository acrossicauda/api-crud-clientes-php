<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 19/03/2021
 * Time: 01:20
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/bd.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/EnderecoController.php';

class UsuariosController extends db {

    public function __construct() {
        $this->conexao = new db();
    }

    /**
     * irá fazer a validação do endereço, separar os daods de cidades, estados e endereços
     * @param array $dados
     * return $idEndereco
     */
    private function cadastrarEndereco($dados = array()) {
        $resp = array();
        $endereco = new EnderecosController();
        if(empty($dados)) {
            $resp = ['success' => false, 'message' => 'Parâmetros incorretos, necessario informar o id do endereço ou os dados que serão cadastrados para esse usuário'];
        } else if(isset($dados['idEndereco']) && !empty(isset($dados['idEndereco']))){
            $filtros = array('campos' => 'enderecos', 'id' => $dados['idEndereco']);
            $resp = $endereco->show($filtros);
            $resp = ['success' => true, 'idEndereco' => $resp['data'][0]['id']];
        } else {
            $resp = $endereco->store($dados);
        }

        return $resp;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store($dadosUsuarios) {
        //return $dadosUsuarios;
        $sucesso = true;
        $message = '';

        $resp = $this->cadastrarEndereco($dadosUsuarios);
        if($resp['success']) {
            $idEndereco = $resp['idEndereco'];
            $dadosUsuarios['idEndereco'] = $idEndereco;
            $camposObrigatorios = array(
                'nome', 'login', 'senha', 'idEndereco'
            );
            foreach ($camposObrigatorios as $k => $value) {
                if(!isset($dadosUsuarios[$value]) || empty($dadosUsuarios[$value])) {
                    $sucesso = false;
                    $message .= "o campo '{$value}' não pode ser vazio\n";
                }

            }
        } else {
            $sucesso = false;
            $message = $resp['message'];
        }
        $idUsuario = '';
        if($sucesso) {

            $nome = $dadosUsuarios['nome'];
            $login = $dadosUsuarios['login'];
            //$senha = bcrypt($dadosUsuarios['senha']);
            $senha = md5($dadosUsuarios['senha']);
            $idEndereco = $dadosUsuarios['idEndereco'];

            try {
                $query = "INSERT INTO usuarios";
                $query .= "(name, login, senha, idEndereco, created_at)";
                $query .= " values('{$nome}', '{$login}', '{$senha}', {$idEndereco}, '" . date('Y-m-d') . "')";
                $this->conexao->executaQuery($query);
                $idUsuario = $this->conexao->getLastId('usuarios', 'id');

                if($idUsuario) {
                    $message = 'Usuario cadastrado';
                } else {
                    $sucesso = false;
                    $message = 'Erro ao tentar cadastrar um Usuário';
                }
            } catch(Exception $e) {
                return "Erro: " . $e->getMessage();
            }

        }

        return ['success' => $sucesso, 'message' => $message, 'idUsuario' => $idUsuario];
    }

    public function validaUser($login, $senha) {
        $ok = false;
        $senha = md5($senha);
        $query = "SELECT name, senha FROM usuarios WHERE login = $login";
        $data = $this->conexao->selectLine($query);
        $ok = md5($senha) == $data['senha'] ? true : false;
        return $ok;
    }

    private function getCountUsuarios($filtros = array()) {
        $filtrosValidos = array('idEndereco' => 'e', 'idCidade' => 'c', 'idEstado' => 'es');
        $resp = array();
        if(!empty($filtros)) {
            $query = "SELECT count(u.id) as count_users
            FROM usuarios as u
            INNER JOIN enderecos as e on e.id = u.idEndereco
            INNER JOIN cidades as c on c.id = e.idEndereco_cidade
            INNER JOIN estados as es on es.id = e.idEndereco_estado";

            $where = array();
            foreach ($filtrosValidos as $k => $v) {
                if((isset($filtros[$k]) && !empty($filtros[$k])) ) {
                    $where[] = " {$v}.id = {$filtros[$k]}";
                }
            }

            if(!empty($where)) {
                $query .= " WHERE ";
                $query .= implode(' AND ', $where);
            }

            $resp = $this->conexao->selectTable($query);

        }
        return $resp;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($campos = array()) {

        // se passar o 'campos' = 'count' irá fazer um count de usuarios utilizando
        // idEndereco, idCidade ou idEstado, ou nenhum dos 3
        if(isset($campos['campos']) && $campos['campos'] == 'count') {
            $resp = $this->getCountUsuarios($campos);
        } else {

            // Irá montar um where com os campos que vierem nesse array
            // então essa var vai servir para que não venha campos desnecessarios
            $validaCampos = array('id', 'name', 'login', 'created_id', 'updated_id');
            $query = "SELECT id, name, login, created_at, updated_at, idEndereco FROM usuarios";
            if(!empty($campos)) {
                $where = array();
                foreach ($campos as $key => $value) {
                    if(in_array($key, $validaCampos)) {
                        $where[] = " {$key} = '$value' ";
                    }
                }
                if(!empty($where)) {
                    $query .= " WHERE ";
                    $query .= implode(' AND ', $where);
                }
            }

            $resp = $this->conexao->selectTable($query);

            $endereco = new EnderecosController();

            foreach ($resp as $k => $value) {
                if(!empty($value['idEndereco'])) {
                    $data = $endereco->show(['id' => $value['idEndereco']]);
                    $resp[$k]->endereco[] = $data['data'];
                }
            }

        }


        return ['data' => $resp];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($campos) {
        // Irá montar um where com os campos que vierem nesse array
        // então essa var vai servir para que não venha campos desnecessarios
        $validaCampos = array('name', 'login', 'senha');
        $message = '';
        if(isset($campos['id']) && !empty($campos['id'])) {

            $idUsuario = $campos['id'];

            $query = "UPDATE usuarios set ";

            if(!empty($campos)) {
                $updateSet[] = "updated_at = '" . date('Y-m-d') . "'";
                foreach ($campos as $key => $value) {
                    if(in_array($key, $validaCampos)) {
                        if($key == 'senha') {
                            $value = md5($value);
                        }
                        $updateSet[] = " {$key} = '$value' ";
                    }
                }
                if(!empty($updateSet)) {
                    $query .= implode(', ', $updateSet);
                }
            }

            $query .= " WHERE id = $idUsuario";

            $resp = $this->conexao->exec($query);
            if($resp) {
                $resp = true;
                $message = 'Usuario Atualizado';
            } else {
                $resp = false;
                $message = 'Erro ao tentar atualizar Usuario';
            }

        } else {
            $resp = false;
            $message = "O campo 'id' é obrigatório";
        }

        return ['success' => $resp, 'message' => $message];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idUsuario) {
        $ok = false;
        $message = '';
        if(!empty($idUsuario)) {
            $ok = $this->conexao->exec("DELETE FROM usuarios where id = $idUsuario");
            if($ok) {
                $message = 'Usuario Excluído';
            } else {
                $message = 'Ocorreu um erro na tentativa de excluir o usuario ' . $idUsuario;
            }
        } else {
            $message = "O campo 'id' não pode ser vazio";
        }

        return ['success' => $ok, 'message' => $message];
    }
}
