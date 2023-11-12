<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

include_once 'helpers/MessageHelper.php';
include_once 'helpers/Constantes.php';
include_once 'includes/CLException.php';
include_once 'includes/CLConstException.php';
include_once 'includes/BaseController.php';

use helpers\MessageHelper;

define('INCLUDE_DIR', dirname(__FILE__) . '/controllers/');
define('BACKEND_URL', (getenv('BACKEND_URL')) ? getenv('BACKEND_URL') : 'http://clube-backend');

// Regras de roteamento
$rules = array(
    'AssuntoController' => "assuntos/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)\/?(?'params'[?\w[=,-.|&A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]*)",
    'AuthController' => "auth/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)",
    'AutorController' => "autores/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)\/?(?'params'[?\w[=,-.|&A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]*)",
    'ChamadoController' => "chamados/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)",
    'ChatController' => "chat/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)",
    'EmprestimoController' => "emprestimos/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)\/?(?'level2'[\w\s]*)",
    'EnderecoController' => "enderecos/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)\/?(?'params'[?\w[=,-.|&]*)",
    'LivroController' => "livros/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)\/?(?'params'[?\w[=,-.|&A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]*)",
    'UsuarioController' => "usuarios/?(?'acao'[\w\-]*)\/?(?'level1'[\w\s]*)"
);


$uri = rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/');
$uri = '/' . trim(str_replace($uri, '', $_SERVER['REQUEST_URI']), '/');
$uri = urldecode($uri);
$method = $_SERVER["REQUEST_METHOD"];

if ($method == 'OPTIONS') {
    exit();
} // Para evitar erros no CORS

foreach ($rules as $controller => $rule) {
    if (preg_match('~^/api/' . $rule . '$~i', $uri, $params)) {
        include(INCLUDE_DIR . $controller . '.php');
        $objeto = new $controller();
        $objeto->processarRequisicao($method, $params);

        exit();
    }
}

// Nenhuma regra foi encontrada.
echo MessageHelper::fmtMsgConstJson('ERR_NAO_ENCONTRADO');