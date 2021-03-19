<?php


/**
 * Class info
 * @package admin
 *
 * Conexao no Google
    'mysql_server' => 'localhost', //https://br406.hostgator.com.br:2083/
    'mysql_user' => 'acrossic',
    'mysql_password' => '',
    'mysql_db' => 'new_portal'//'sal_database'
 */
class info {

    private $url;
    private $dadosBanco = [
        'mysql_server' => 'localhost',
        'mysql_user' => 'root',
        'mysql_password' => '',
        'mysql_db' => 'api_endereco'
    ];
    // banco local, é validado no construct se a URL tem 'localhost'
    private $dadosBancoLocal = [
        'mysql_server' => 'localhost',
        'mysql_user' => 'root',
        'mysql_password' => '',
        'mysql_db' => 'api_endereco'
    ];

    public static function dataBD() {
        //return $this->dadosBanco;
    }
    public function mysql_server() {
        return $this->dadosBanco['mysql_server'];
    }
    public function mysql_user() {
        return $this->dadosBanco['mysql_user'];
    }
    public function mysql_password() {
        return $this->dadosBanco['mysql_password'];
    }
    public function mysql_db() {
        return $this->dadosBanco['mysql_db'];
    }
    public function config_url() {
        $url = array_filter(explode('/',$_SERVER['REQUEST_URI']));
        return $this->url = (isset($url[0])) ? $url[0] : $url[1];
    }
    public function __construct() {
        // Apenas se estiver no localhost mudar dados do banco
        if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
            $this->dadosBanco = $this->dadosBancoLocal;
        }
    }
}
