<?php
session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome = $_POST['nome_fornecedor'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $email = $_POST['email'];
    $contato = $_POST['contato'];
    $id_usuario = $_POST['id_fornecedor'];


    // ATUALIZA OS DADOS DO USUARIO
    if ($nova_senha){
        $sql = "UPDATE fornecedor SET nome_fornecedor = :nome_fornecedor, telefone = :telefone, endereco = :endereco, email = :email, contato = :contato WHERE id_fornecedor = :id";
        $stmt = $pdo->prepare($sql);
} else{
    $sql = "UPDATE fornecedor SET nome_fornecedor = :nome_fornecedor, telefone = :telefone, endereco = :endereco, email = :email, contato = :contato WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    }
    $stmt->bindParam(":nome_fornecedor", $nome);
    $stmt->bindParam(":telefone", $telefone);
    $stmt->bindParam(":endereco", $endereco);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":contato", $contato);
    $stmt->bindParam(":id", $id_usuario);

    if($stmt->execute()){
        echo "<script>alert('Fornecedor atualizado com sucesso!'); window.location.href='buscar_fornecedor.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar fornecedor.'); window.location.href='alterar_fornecedor.php?id=$id_fornecedor';</script>";
    }
}