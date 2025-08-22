<?php
session_start();
require_once 'conexao.php';

//verifica se usuario tem permissao de adm
if($_SESSION['perfil']!=1){
    echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
    exit();
}

// OBTENDO O NOME DO PERFIL DO USUARIO LOGADO
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// Permissoes
$permissoes = [
    // permissoes adm
    1 => ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
          "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
          "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
          "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],
    //permissoes secretaria
    2 => ["Cadastrar"=>["cadastro_cliente.php"],
          "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
          "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
          "Excluir"=>["excluir_produto.php"]],
    // permissoes almoxarife
    3 => ["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],
          "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
          "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
          "Excluir"=>["excluir_produto.php"]],
    // permissoes cliente
    4 => ["Cadastrar"=>["cadastro_cliente.php"],
          "Buscar"=>["buscar_cliente.php"],
          "Alterar"=>["alterar_cliente.php"]],
];


// obtém as opções disponíveis para o perfil
$opcoes_menu = $permissoes[$id_perfil];

//inicializa variavel para armazenar fornecedor
$fornecedor = [];

//busca todos os fornecedores cadastrados em ordem alfabetica
$sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
$stmt=$pdo->prepare($sql);
$stmt->execute();
$fornecedor = $stmt->fetchAll(PDO::FETCH_ASSOC);

//se um id for passado via get exclui fornecedor
if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id_fornecedor = $_GET['id'];

    //exclui o fornecedor do banco 
    $sql="DELETE FROM fornecedor WHERE id_fornecedor = :id";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id',$id_fornecedor,PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('Fornecedor excluído com sucesso!'); window.location.href='excluir_fornecedor.php';</script>";
    }else{
        echo "<script>alert('Erro ao excluir fornecedor!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Fornecedor</title>
    <link rel="stylesheet" href="styles.css">
</head>
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

<body>
    <!-- Menu do sistema -->
    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo): ?>
                            <li><a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <h2>Excluir Fornecedor</h2>
    <?php if(!empty($fornecedor)): ?>
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

            <?php foreach($fornecedor as $fornecedor): ?>
                <tr>
                <td> <?= htmlspecialchars($fornecedor['id_fornecedor']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['telefone']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['endereco']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['email']) ?> </td>
                <td> <?= htmlspecialchars($fornecedor['contato']) ?> </td>
                <td> 
                <a href="excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')"> Excluir </a>
                </td>
            </tr>
            <?php endforeach; ?> 
        </table>
    <?php else: ?>
        <p>Nenhum fornecedor encontrado</p>
    <?php endif; ?>
    
    <br>
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