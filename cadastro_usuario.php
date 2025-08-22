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
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'],PASSWORD_DEFAULT);
    $id_perfil = $_POST['id_perfil'];

   // VALIDAÇÃO PARA NOME
   if (preg_match('/[^a-zA-Z\s]/', $nome)) {
    echo "<script>alert('Nome não pode conter simbolos!'); window.location.href='cadastro_usuario.php';</script>";
  exit();
}

    $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) VALUES (:nome, :email, :senha, :id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':id_perfil', $id_perfil);
    if ($stmt->execute()) {
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar usuário!');</script>";
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
    <title>Cadastro de Usuários</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Cadastrar Usuário</h2>
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
    <form action="cadastro_usuario.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
        
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        
        <label for="id_perfil">Perfil:</label>
        <select id="id_perfil" name="id_perfil" required>
        <option value="1">Administrador</option>
        <option value="2">Secretária</option>
        <option value="3">Almoxarife</option>
        <option value="4">Cliente</option>
        </select>
        
        <button type="submit">Cadastrar</button>
        <button type="reset">Cancelar</button>
    </form>
    <a href="principal.php">Voltar</a>

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