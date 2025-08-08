<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes_email.php'; // ARQUIVO COM AS FUNÇÕES QUE GERAM A SENHAM E SIMULAM O ENVIO DE E-MAIL

if ($_SERVER['REQUEST_METHOD']== "POST") {
    $email = $_POST['email'];

    // VERIFICA SE O EMAIL EXISTE NO BANCO DE DADOS
    $sql = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // GERA UMA NOVA SENHA TEMPORÁRIA
        $senha_temporaria = gerarSenhaTemporaria();
        $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

        
        // ATUALIZA A SENHA DO USUARIO NO BANCO
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = TRUE WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // SIMULA O ENVIO DO EMAIL (GRAVA EM TXT)
        simularEnvioEmail($email, $senha_temporaria);
        echo "<script>alert('Uma nova senha foi enviada(simulação). Verifique o arquivo emails_simulados.txt.');window.location.href = 'login.php';
        </script>";
    
    
    }
}