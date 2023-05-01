<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once 'helpers/MessageHelper.php';
include_once 'helpers/Constantes.php';
include_once 'includes/CLException.php';
include_once 'includes/CLConstException.php';
include_once 'includes/BaseController.php';


define( 'INCLUDE_DIR', dirname( __FILE__ ) . '/controllers/' );

// Regras de roteamento
$rules = array( 
    'AuthController'        => "auth/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)",
    'AutorController'       => "autores/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)",
    'ChamadoController'     => "chamados/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)",
    'EmprestimoController'  => "emprestimos/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)\/?(?'param2'[\w\s]*)",
    'EnderecoController'    => "enderecos/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)",
    'AssuntoController'     => "assuntos/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)",
    'LivroController'       => "livros/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)",
    'UsuarioController'     => "usuarios/?(?'acao'[\w\-]*)\/?(?'param1'[\w\s]*)"
);


$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
$uri = urldecode( $uri );
$method = $_SERVER["REQUEST_METHOD"];

if ($method=='OPTIONS') { exit(); } // Para evitar erros no CORS

foreach ( $rules as $controller => $rule ) {
    if ( preg_match( '~^/api/'.$rule.'$~i', $uri, $params ) ) {
        include( INCLUDE_DIR . $controller . '.php' );
        $objeto = new $controller();
        $objeto->processarRequisicao( $method, $params);

        exit();
    }
}

// Nenhuma regra foi encontrada.
include( dirname( __FILE__ ) . '/404.php' );