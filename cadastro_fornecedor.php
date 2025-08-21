<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSAO
// SUPONDO QUE O PERFIL 1 SEJA ADMINISTRADOR

if ($_SESSION['perfil']!=1) {
    echo "Acesso Negado!";
}
if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $contato = $_POST['contato'];

    // VALIDAÇÃO PARA NOME
    if (preg_match('/[^a-zA-Z\s]/', $nome)) {
        echo "<script>alert('Nome não pode conter simbolos!'); window.location.href='cadastro_fornecedor.php';</script>";
      exit();
    }

    $sql = "INSERT INTO fornecedor (nome_fornecedor, endereco, telefone, email, contato) VALUES (:nome_fornecedor, :endereco, :telefone, :email, :contato)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_fornecedor', $nome);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contato', $contato);
    if ($stmt->execute()) {
        echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar Fornecedor!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Fornecedores</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Cadastrar Fornecedor</h2>
    <form action="cadastro_fornecedor.php" method="POST">
        <label for="nome">Nome do Fornecedor:</label>
        <input type="text" id="nome" name="nome" required>
        <br>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required>
        <br>

        <label for="telefone">Telefone:</label>
        <input type="tel" id="telefone" name="telefone" required>
        <br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <label for="contato">Nome para contato:</label>
        <input type="text" id="contato" name="contato" required>
        <br>
         
        <button type="submit">Cadastrar</button>
        <button type="reset">Cancelar</button>
    </form>
    <a href="principal.php">Voltar</a>

      <!-- Máscaras de entrada -->
  <script src="https://cdn.jsdelivr.net/npm/inputmask/dist/inputmask.min.js"></script>
  <script>
    Inputmask({ mask: "(99) 99999-9999" }).mask("#telefone");
  </script>

</body>
</html>