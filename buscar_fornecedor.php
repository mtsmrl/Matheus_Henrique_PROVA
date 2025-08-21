<?php
    session_start();
    require_once "conexao.php";

    // VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM OU SECRETÁRIA
    if ($_SESSION['perfil'] !=1 && $_SESSION['perfil'] !=2) {
        echo "<script>alert('Acesso Negado!'); window.location.href='index.php';</script>";
        exit();
    }

    $usuario = [];  // INICIALIZA A VARÁVEL PARA EVITAR ERROS

    // SE O FORMULÁRIO FOR ENVIADO, BUSCA O USUÁRIO PELO ID OU NOME
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
        $busca = trim($_POST['busca']);
        
        // VERIFICA SE A BUSCA É UM NÚMERO OU UM NOME
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca ORDER BY nome_fornecedor ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":busca", $busca, PDO::PARAM_INT);
        } else {
            // VALIDAÇÃO PARA O NOME
            if (preg_match('/[^a-zA-Z\s]/', $busca)) {
                echo "<script>alert('Nome não pode conter símbolos!');</script>";
                exit;
            }

            $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor LIKE :busca_nome ORDER BY nome_fornecedor ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":busca_nome", "$busca%", PDO::PARAM_STR);
        }
    } else {
        $sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
        $stmt = $pdo->prepare($sql);
    }
    $stmt->execute();
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Buscar Usuário </title>
    <link rel="stylesheet" href="styles.css">
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
    <h2> Lista de Fornecedores </h2>
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
    
    <form action="buscar_fornecedor.php" method="POST">
        <label for="busca"> Digite o ID ou NOME do Fornecedor: </label>
        <input type="text" name="busca" id="busca" required>
        
    </form>

    <?php if (!empty($fornecedores)) { ?>
        <table border="1">
            <tr>
                <th> ID </th>
                <th> Nome</th>
                <th> Telefone </th>
                <th> Endereço </th>
                <th> E-mail </th>
                <th> Contato </th>
                <th> Ações </th>
            </tr>

            <?php foreach ($fornecedores as $fornecedor) { ?>
            <tr>
                <td> <?= htmlspecialchars($fornecedor['id_fornecedor']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['telefone']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['endereco']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['email']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['contato']) ?> </td>
                <td> 
                    <a href="alterar_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>"> Alterar </a>
                    <a href="excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')"> Excluir </a>
                </td>
            </tr>
            <?php } ?>
        </table>

    <?php } else { ?>
        <p> Nenhum fornecedor encontrado. </p>
    <?php } ?>

    <a href="principal.php"> Voltar  </a>
</body>
</html>