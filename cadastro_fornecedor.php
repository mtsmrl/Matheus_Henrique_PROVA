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

// OBTENDO O NOME DO PERFIL DO USUÁRIO LOGADO
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// DEFINIÇÃO DAS PERMISSÕES POR PERFIL
$permissoes = [
    // PERMISSÕES DO ADMIN
    1 => ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
          "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
          "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
          "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

    // PERMISSÕES DA SECRETÁRIA
    2 => ["Cadastrar"=>["cadastro_cliente.php"],
          "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
          "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
          "Excluir"=>["excluir_produto.php"]],

    // PERMISSÕES DO ALMOXARIFE
    3 => ["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],
          "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
          "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
          "Excluir"=>["excluir_produto.php"]],

    // PERMISSÕES DO CLIENTE
    4 => ["Cadastrar"=>["cadastro_cliente.php"],
          "Buscar"=>["buscar_cliente.php"],
          "Alterar"=>["alterar_cliente.php"]],
];

// OBTENDO AS OPÇÕES DISPONIVEIS PARA O PERFIL DO USUÁRIO LOGADO
$opcoes_menu = $permissoes["$id_perfil"];
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
<nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos) { ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>

                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo) { ?>
                            <li>   
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>
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

<br>
    <br>
    <br>
    <br>
    <br>
    

    <adress>
        <center>
            Matheus Henrique Coelho Amaral / Técnico em Desenvolvimento de Sistemas / 2025
    </center>
    </adress>
</body>
</html>