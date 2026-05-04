<?php
//login.php - serve para logar no sistema se existir o usuário
require '../../app/conexao.php';
$pdo = Conexao::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//$json = $_GET['jsn'];
$json = filter_input(INPUT_GET,'jsn');
$data = json_decode($json,true);//{"usuario":"'' or '1' = '1'; -- ","senha":""}
$usuario = $data['usuario'];//'' OR '1'='1'
$senha = $data['senha'];//
//"select * from usuarios where usulogin = '' OR '1'='1'; -- and ususenha = MD5();"
$sql = "select usuid as id, usunome as nome, usulogin as usuario, usulogado as logado from usuarios where usulogin = ? and ususenha = MD5(?);";
$prp = $pdo->prepare($sql);
$prp->execute([$usuario,$senha]);
//$prp->execute();
$data = $prp->fetchall(PDO::FETCH_ASSOC);
echo json_encode($data);
Conexao::desconectar();
//{"nome":"valor"}
//http://localhost/Projetos_ETEC_PWEB-III_Div1/api/login.php?jsn={"usuario":"xandao","senha":"123456"}