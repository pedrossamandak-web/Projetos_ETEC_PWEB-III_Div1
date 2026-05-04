<?php
//iusuario.php - serve para cadastrar um novo usuário
require '../../app/conexao.php';
$pdo = Conexao::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = filter_input(INPUT_GET, 'jsn');
/*
"op":"i" = insert,"op":"u" = update,"op":"d" = delete,"op":"sp" = select com parametros,"op":"l" = login
*/
$data = json_decode($json, true);
/*{"op":"sp","id":"1","nome":"RICARDO DA SILVA ZANATA","usuario":"RICK'S","senha":"123456","logado":"TRUE"}*/
/* Operador de Coalescência Nula (??) -  ?? ''*/
$op = $data['op'] ?? '';
$id = $data['id'] ?? '';
$nome = $data['nome'] ?? '';
$usuario = $data['usuario'] ?? '';
$senha = $data['senha'] ?? '';
$logado = $data['logado'] ?? '';

switch ($op) {
    case 'i':
        $sql = "insert into usuarios (usunome, usulogin, ususenha) values (?,?,MD5(?));";
        $prp = $pdo->prepare($sql);
        $prp->execute([$nome, $usuario, $senha]);
        break;
    case 'u':
        if (empty($data['senha'])) {
            $sql = "update usuarios set usunome = ?, usulogin = ?, usulogado = ? where usuid = ?;";
            $prp = $pdo->prepare($sql);
            $prp->execute([$nome, $usuario, $logado, $id]);
        } else {
            $senha = $data['senha'];
            $sql = "update usuarios set usunome = ?, usulogin = ?, ususenha = MD5(?), usulogado = ? where usuid = ?;";
            $prp = $pdo->prepare($sql);
            $prp->execute([$nome, $usuario, $senha, $logado, $id]);
        }
        break;
    case 'd':
        $sql = "delete from usuarios where usuid = ?;";
        $prp = $pdo->prepare($sql);
        $prp->execute([$id]);
        break;
    case 'l':
        $sql = "
select 
usuid as id, 
usunome as nome, 
usulogin as usuario, 
usulogado as logado 
from usuarios 
where usulogin = ? 
and ususenha = MD5(?);
";
        $prp = $pdo->prepare($sql);
        $prp->execute([$usuario, $senha]);
        $data = $prp->fetchall(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;
    case 'sp':
        $nome = '%' . $nome . '%';
        $sql = "
select 
usuid as id, 
usunome as nome, 
usulogin as usuario, 
usulogado as logado 
from usuarios 
where usunome like ?;
";
        $prp = $pdo->prepare($sql);
        $prp->execute([$nome]);
        $data = $prp->fetchall(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;
    default:
        echo "Parametros de entrada incorretos";
}
Conexao::desconectar();
