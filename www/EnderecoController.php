<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 19/03/2021
 * Time: 01:20
 */


require_once $_SERVER['DOCUMENT_ROOT'] . '/config/bd.php';
class EnderecosController extends db {

    private $db;

    public function __construct() {
        $this->conexao = new db();
    }

    // a função irá fazer o insert passando a tabela e os parametros
    private function cadEndCidEst($table, $dados) {
        // Verifica se o cadastro ja existe
        $query = "SELECT id
            FROM {$table}
            WHERE ";
        $where = array();
        foreach ($dados as $k => $v) {
            if(!empty($v)) {
                $v = str_replace("'", "", $v);
                $where[] = " {$k} = '{$v}' ";
            }
        }
        $query .= implode(' AND ', $where);

        $id = DB::select($query);
        if(!$id) {
            $id = DB::table($table)->insertGetId(
                $dados
            );
        } else {
            $id = $id[0]->id;
        }

        return $id;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($dadosEndereco) {

        $sucesso = true;
        $message = '';

        $camposObrigatorios = array(
            'tipoLogradouro', 'logradouro', 'cep',
            'estado', 'uf',
            'cidade'
        );

        foreach ($camposObrigatorios as $k => $value) {
            if(!isset($dadosEndereco[$value]) || empty($dadosEndereco[$value])) {
                $sucesso = false;
                $message .= "o campo '{$value}' não pode ser vazio\n";
            }
        }

        // Separando campos de endereco
        foreach (array('tipoLogradouro', 'logradouro', 'cep', 'numero') as $item) {
            if(isset($dadosEndereco[$item]) && !empty($dadosEndereco[$item])) {
                $dadosEnde[$item] = $dadosEndereco[$item];
            }
        }
        // Separando campos de cidade
        foreach (array('cidade') as $item) {
            if(isset($dadosEndereco[$item]) && !empty($dadosEndereco[$item])) {
                $dadosCidades[$item] = $dadosEndereco[$item];
            }
        }
        // Separando campos de estado
        foreach (array('estado', 'uf') as $item) {
            if(isset($dadosEndereco[$item]) && !empty($dadosEndereco[$item])) {
                $dadosEstados[$item] = $dadosEndereco[$item];
            }
        }

        $idEndereco = '';
        if($sucesso) {

            try {

                $idEstado = $this->cadEndCidEst("estados", $dadosEstados);

                if($idEstado)
                    $dadosCidades['idCidade_estado'] = $idEstado;

                $idCidade = $this->cadEndCidEst("cidades", $dadosCidades);

                if($idEstado)
                    $dadosEnde['idEndereco_estado'] = $idEstado;
                if($idCidade)
                    $dadosEnde['idEndereco_cidade'] = $idCidade;
                $idEndereco = $this->cadEndCidEst("enderecos", $dadosEnde);

                if($idEndereco) {
                    $message = 'Endereço cadastrado';
                } else {
                    $sucesso = false;
                    $message = 'Erro ao tentar cadastrar um novo Endereço';
                }
            } catch(Exception $e) {
                return "Erro: " . $e->getMessage();
            }

        }

        return ['success' => $sucesso, 'message' => $message, 'idEndereco' => $idEndereco];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($campos = array()) {
        // Irá montar um where com os campos que vierem nesse array
        // então essa var vai servir para que não venha campos desnecessarios
        $validaCamposEnd = array('id', 'tipoLogradouro', 'logradouro', 'cep', 'numero');
        $validaCamposCid = array('id', 'cidade');
        $validaCamposEst = array('id', 'estado', 'uf');
        $ids = array('idEndereco' => 'e.id', 'idEstado' => 'es.id', 'idCidade' => 'c.id');
        // campos que são permitidos no retorno
        $camposValidos = array('enderecos', 'cidades', 'estados');
        $query = "SELECT e.id as idEndereco, e.tipoLogradouro, e.logradouro, e.cep, e.numero,
         c.id as idCidade, c.cidade,
         es.id as idEstado, es.estado, es.uf
        FROM enderecos as e
        LEFT JOIN cidades as c on e.idEndereco_cidade = c.id
        LEFT JOIN estados as es on (es.id = c.idCidade_estado OR es.id = c.idCidade_estado)";

        // essa var só sera usada para não montar o where com os campos das outras tabelas, se
        // for informado o parametro 'campos'
        $table = '';
        if(!empty($campos)) {
            if( (isset($campos['campos']) && !empty($campos['campos'])) && in_array($campos['campos'], $camposValidos) ) {
                $table = $campos['campos'];
                $query = " SELECT * FROM " . $table;
                $query .= ($table == 'cidades') ? " as c" : (($table == 'estados') ? " as es" : " as e");
                unset($campos['campos']);
            }

            $where = array();
            foreach ($campos as $key => $value) {
                if(in_array($key, $validaCamposEnd) && !in_array($table, ['cidades', 'estados']) ) {
                    $where[] = " e.{$key} = '$value' ";
                } else if(in_array($key, $validaCamposCid) && !in_array($table, ['enderecos', 'estados']) ) {
                    $where[] = " c.{$key} = '$value' ";
                } else if(in_array($key, $validaCamposEst) && !in_array($table, ['enderecos', 'cidades'])) {
                    $where[] = " es.{$key} = '$value' ";
                } else if(in_array($key, array_keys($ids))) {
                    $where[] = " {$ids[$key]} = '$value' ";
                } else {
                    return ['success' => false, 'message' => 'Parâmetro informado é inválido'];
                }
            }
            if(!empty($where)) {
                $query .= " WHERE ";
                $query .= implode(' AND ', $where);
            }
        }

        $resp = $this->conexao->selectTable($query);

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
        $listEnd = array('tipoLogradouro', 'logradouro', 'cep', 'numero');
        $listCid = array('cidade');
        $listEst = array('estado', 'uf');

        // Irá montar um where com os campos que vierem nesse array
        // então essa var vai servir para que não venha campos desnecessarios
        $validaCampos = array('tipoLogradouro', 'logradouro', 'cep');
        $message = '';
        if(isset($campos['id']) && !empty($campos['id'])) {

            $id = $campos['id'];
            $tables = array();
            if(!empty($campos)) {
                foreach ($campos as $k => $v) {
                    if(in_array($k, $listEnd))
                        $tables[] = 'enderecos';
                    if(in_array($k, $listCid))
                        $tables[] = 'cidades';
                    if(in_array($k, $listEst))
                        $tables[] = 'estados';
                }
            }
            foreach ($tables as $table) {

                $query = "UPDATE $table set ";

                if(!empty($campos)) {
                    foreach ($campos as $key => $value) {
                        if(in_array($key, $validaCampos)) {
                            $updateSet[] = " {$key} = '$value' ";
                        }
                    }
                    if(!empty($updateSet)) {
                        $query .= implode(', ', $updateSet);
                    }
                }

                $query .= " WHERE id = $id";

                $resp = $this->conexao->exec($query);
                //$resp = DB::update($query);


                if($resp) {
                    $resp = true;
                    $message = 'Usuario Atualizado';
                } else {
                    $resp = false;
                    $message = 'Erro ao tentar atualizar Usuario';
                    break;
                }
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
    public function destroy($idEndereco) {
        return ['success' => false, 'message' => "Metódo não habilitado"];
        $ok = false;
        $message = '';
        if(!empty($idEndereco)) {
            $ok = $this->conexao->exec("DELETE FROM usuarios where id = $idEndereco");
            if($ok) {
                $message = 'Usuario Excluído';
            } else {
                $message = 'Ocorreu um erro na tentativa de excluir o usuario ' . $idEndereco;
            }
        } else {
            $message = "O campo 'id' não pode ser vazio";
        }
        //DB::beginTransaction();
        //DB::commit();
        //DB::rollback();
        return ['success' => $ok, 'message' => $message];
    }
}
