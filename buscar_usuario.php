<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSAO DE ADMIN OU SECRETARIA
if($_SESSION['perfil'] !=1 && $_SESSION['perfil']!=2){
    echo "<script>alert('Acesso Negado!');window.location.href = 'principal.php';</script>";
    exit();
    
}

$usuario = []; // INICIALIZA A VARIAVEL PARA EVITAR ERROS

// SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO ID OU NOME
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    // VERIFICA SE A BUSCA É UM NUMERO OU UM NOME
    if(is_numeric($busca)){
    $sql = "SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
    $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    } 
} else {
    $sql = "SELECT * FROM usuario ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
}
$stmt->execute();
    $usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>