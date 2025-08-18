<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSAO DE ADMIN
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
    exit();
}

// INICIALIZA VARIAVEL PARA ARMAZENAR USUÁRIOS
$usuarios = [];

// BUSCA TODOS OS USUÁRIOS CADASTRADOS EM ORDEM ALFABETICA
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SE UM ID FOR PASSADO VIA GET, EXCLUI O USUÁRIO
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];
    // EXCLUI O USUÁRIO DO BANCO DE DADOS
    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
  
    if ($stmt->execute()) {
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='excluir_usuario.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir usuário.'); window.location.href='excluir_usuario.php';</script>";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel = "stylesheet" href = "styles.css">
    <style>
  table {
    border-collapse: collapse;
    width: 100%;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
  }

  th {
    background-color: #f0f0f0;
  }

  tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  tr:hover {
    background-color: #ddd;
  }

  .botao-acao {
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .botao-acao:hover {
    background-color: #ccc;
  }

  .botao-alterar {
    background-color: #4CAF50;
    color: #fff;
  }

  .botao-excluir {
    background-color: #e74c3c;
    color: #fff;
  }

  .botao-voltar {
    background-color: #3498db;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .botao-voltar:hover {
    background-color: #2ecc71;
  }
</style>
</head>
<body>
    <h2>Excluir Usuário</h2>
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
    <?php if (!empty($usuarios)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?=htmlspecialchars($usuario['id_usuario'])?></td>
                    <td><?=htmlspecialchars($usuario['nome'])?></td>
                    <td><?=htmlspecialchars($usuario['email'])?></td>
                    <td><?=htmlspecialchars($usuario['id_perfil'])?></td>
                    <td>
                        <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Nenhum usuário cadastrado.</p>
        <?php endif; ?>
        <a href="principal.php">Voltar</a>
</body>
</html>