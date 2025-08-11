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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2> Lista de Usuario </h2>
    <form action = "buscar_usuario.php" method = "POST">
        <label for="busca"> Digite o ID ou NOME do Usuario (Opcional): </label>
        <input type="text" id="busca" name="busca">
    </form>
    <?php if (!empty($usuarios)): ?>
    <table>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th>Ações</th>
          </tr>  
          <?php foreach ($usuarios as $usuario): ?>

            <tr>
            <td><?=htmlspecialchars($usuario['id_usuario']);?></td>ID</td>
            <td><?=htmlspecialchars($usuario['nome']);?></td>Nome</td>
            <td><?=htmlspecialchars($usuario['email']);?></td>E-mail</td>
            <td><?=htmlspecialchars($usuario['id_perfil']);?></td>Perfil</td>

          </tr>
           
    </table>
        
</body>
</html>