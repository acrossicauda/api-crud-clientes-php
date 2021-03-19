<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 19/03/2021
 * Time: 02:03
 */

require_once $_SERVER['DOCUMENT_ROOT']. "/config/db_data.php";
class db extends info {
    protected $mysql_server;
    protected $mysql_user;
    protected $mysql_password;
    protected $mysql_db;//'sal_database';

//    protected $mysql_server = 'localhost';
//    protected $mysql_user = 'root';
//    protected $mysql_password = '';
//    protected $mysql_db = 'new_portal';//'sal_database';


    function __construct() {
        parent::__construct();
        $this->mysql_server = $this->mysql_server();
        $this->mysql_user = $this->mysql_user();
        $this->mysql_password = $this->mysql_password();
        $this->mysql_db = $this->mysql_db();//'sal_database';

    }

    /*
     * @todo
     * Cria a conexao com o banco
     */
    function conn() {
        $mysqli = mysqli_connect($this->mysql_server, $this->mysql_user, $this->mysql_password, $this->mysql_db);
        if ($mysqli === false) {
            $mysqli = ("Connection failed: %s \n" . $mysqli->connect_error);
            exit();
        }
        $mysqli->set_charset("utf8");
        return $mysqli;
    }

    function closeConn() {
        $this->conn()->close();
    }
    /*
     * @todo
     * Traz um valor ou linha de forma associativa nominal, ou seja, com o nome dos campos como indice
     */
    function selectLine($query) {
        // $resultado = mysqli_query($this->conn(), $query);
        // $res = $resultado->fetch_all();
        $resultado = mysqli_query($this->conn(), $query);
        $res = mysqli_fetch_assoc($resultado);
        return $res;
    }

    /* @todo
     * Seleciona todos os dados de forma associativa nominal, ou seja, com os nomes de cada campo como indice
     */
    function selectTable($query) {
        $resultado = mysqli_query($this->conn(), $query);
        if (!$resultado) {
            printf("Error: %s\n", mysqli_error($this->conn()));
            //exit();
        }
        $i = 0;
        if(!empty($resultado)) {
            //while ($res[$i] = mysqli_fetch_assoc($resultado)) {
            while ($res[$i] = $resultado->fetch_assoc()) {
                //$res[$i] = mysqli_fetch_assoc($resultado);
                $i++;
            }
        } else {
            $res = null;
        }
        return array_filter($res);
    }

    /*
    * @todo
    * Seleciona todos os campos de forma associativa numeria, ou seja, com indices numericos
    */
    function selectAll($query) {
        $res = array();
        $resultado = mysqli_query($this->conn(), $query);
        if (!$resultado) {
            printf("Error: %s\n", mysqli_error($this->conn()));
            //exit();
        }
        if($this->conn()->query($query)) {
            foreach($resultado->fetch_array(MYSQLI_ASSOC) as $key => $val) {
                $res[] = $val;
            }
        } else {
            $res = ['error' => 'Erro'];
        }


        return $res;
        // return $resultado->fetch_array(MYSQLI_NUM);
    }

    /*
     * @todo
     */
    function selectQtd($query) {
        $result = mysqli_query($this->conn(), $query);
        return $result->num_rows;
    }


    public function lastInsertId() {
        //mysqli_commit($this->conn());
        $id = $this->selectLine("SELECT LAST_INSERT_ID()");
        if($id['LAST_INSERT_ID()'] != 0 && $id['LAST_INSERT_ID()'] != '') {
            return $id['LAST_INSERT_ID()'];
        } else if($this->conn()->insert_id != 0 && $this->conn()->insert_id != '') {
            return $this->conn()->insert_id;
        } else if(mysqli_insert_id($this->conn()) != 0 && mysqli_insert_id($this->conn()) != ''){
            return mysqli_insert_id($this->conn());
        } else {
            //$this->logs('Erro ao tentar pegar o ultimo ID Cadastrado', 'erro_'.__FUNCTION__.'.txt');
        }
    }

    function getLastId($table, $idName = '') {
        $id = $this->selectLine("SELECT {$idName} from usuarios order by {$idName} desc limit 1");
        return $id[$idName];
    }

    /*
     * @todo
     * executa as querys, por exemplo, update ou delete e retorna o indice alterado
     */
    function executaQuery($query) {
        $ok = mysqli_query($this->conn(), $query);
        // $teste = "New Record has id " . mysqli_insert_id($this->conn());
        //return $this->selectLine("SELECT LAST_INSERT_ID()");
        return mysqli_insert_id($this->conn());
    }


    /*
     * @todo
     * executa as querys, por exemplo, update ou delete e retorna o indice alterado
     * Teste
     */
    function executaQueryReturnId($query) {
        //mysqli_query($this->conn(), $query);
        $this->conn()->prepare($query);
        $this->conn()->execute($query);
        // $teste = "New Record has id " . mysqli_insert_id($this->conn());
        //return $this->selectLine("SELECT LAST_INSERT_ID()");

        return $this->conn()->mysqli_insert_id;
    }

    /**
     * @todo
     * @param $query
     * @return int|string
     * Function criada para usar com DELETE e retornar o resultado, pois não retorna ID Insert no DELETE
     *
     * https://www.php.net/manual/pt_BR/mysqli.insert-id.php
     */
    function exec($query) {
        $res = mysqli_query($this->conn(), $query);
        //mysqli_commit($this->conn());
        return $res;
    }

}