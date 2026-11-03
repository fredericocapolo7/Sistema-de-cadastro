<?php
// include_once("conexao.php") ela serve para incluir o arquivo de conexão com o banco de dados, permitindo que este arquivo utilize a conexão estabelecida no arquivo conexao.php. Isso é útil para manter o código organizado e evitar duplicação de código, pois a conexão com o banco de dados pode ser reutilizada em diferentes partes do sistema sem precisar reescrever a lógica de conexão.;
include_once("conexao.php"); 

$resposta = [];
header('Content-Type: application/json; charset=utf-8');

if (isset($erroConexao)) {
    echo json_encode([
        'erro' => 'sim',
        'getErro' => 'Não foi possível conectar ao banco de dados.'
    ]);
    exit;
}
//isset($_POST["_nome"]) verifica se o campo "_nome" foi enviado via método POST. Se o campo estiver presente, o código dentro do bloco if será executado, permitindo que o cadastro seja processado apenas quando os dados necessários forem fornecidos pelo usuário.;
if(isset($_POST["_nome"])){ //isset serve para verificar se a variável $_POST["_nome"] está definida e não é nula. Isso é importante para garantir que o código dentro do bloco if só seja executado quando o formulário de cadastro for enviado com o campo "_nome" preenchido.;

// apneas para completar as variaves no php buscamos as variaves que estao definido no javascript exmplo abaixo:
    $nome     = $_POST["_nome"];
    $fone     = $_POST["_fone"];
    $email    = $_POST["_email"];   
    $endereco = $_POST["_endereco"];
    $senha    = $_POST["_senha"];

    //Tratamento dos dados.Segurança contra sql injection.
    $nome = filter_var($nome, FILTER_SANITIZE_STRING); 
    $fone = filter_var($fone, FILTER_SANITIZE_NUMBER_INT); 
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $endereco = filter_var($endereco, FILTER_SANITIZE_STRING);
    $senha = filter_var($senha, FILTER_SANITIZE_STRING);

    $perfil   = 'Usuário';
    $cod      = rand(1000, 999999);
    $estatus  = 'Pendente';
    $dataHora = date('Y-m-d H:i:s');

    //validacao de email.
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $resposta['erro'] = 'sim';
        $resposta['getErro'] = 'O email informado não é válido.';
        echo json_encode($resposta);
        exit;
    }

    // Verificação se o email já está cadastrado no banco de dados. 
    $sqlVerificaEmail = $conexao->prepare('SELECT * FROM `login` WHERE email = ?');//$sqlVerificaEmail = $conexao->prepare('SELECT * FROM `login` WHERE email = ?') prepara uma consulta SQL para selecionar todos os registros da tabela "login" onde o campo "email" corresponde ao valor fornecido pelo usuário. 

    $sqlVerificaEmail->execute([$email]); // $sqlVerificaEmail->execute([$email]) executa a consulta SQL preparada, substituindo o marcador de parâmetro (?) pelo valor do email fornecido pelo usuário. Isso ajuda a prevenir ataques de SQL injection, garantindo que o valor do email seja tratado de forma segura.;
    
    if($sqlVerificaEmail->rowCount() > 0){ // $sqlVerificaEmail->rowCount() retorna o número de linhas afetadas pela consulta SQL executada. 

        $resposta['erro'] = 'sim';
        $resposta['getErro'] = 'O email informado já está cadastrado.';
        echo json_encode($resposta);
        exit;
    }


    //validacao de senha, e senha segura.
    if(strlen($senha) < 8){
        $resposta['erro'] = 'sim';
        $resposta['getErro'] = 'A senha deve ter no mínimo 8 caracteres.';
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE); // echo json_encode($resposta) converte o array $resposta em uma string JSON, que é então enviada como resposta para o cliente. Isso permite que o cliente (por exemplo, um navegador ou aplicativo) receba os dados de forma estruturada e possa processá-los facilmente. json unescaped_unicode é usado para garantir que os caracteres Unicode sejam exibidos corretamente no JSON, sem serem escapados.;
        exit;
    }
       if(!preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha)){
        $resposta['erro'] = 'sim';
        $resposta['getErro'] = 'A senha deve conter pelo menos uma letra maiúscula, uma letra minúscula e um número.';
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
        exit;
    }

      if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $senha)) {
        $resposta['erro'] = 'sim';
        $resposta['getErro'] = 'A senha deve conter pelo menos um caractere especial.';
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // criptografia de senha, para garantir a segurança dos dados do usuário. 
    $senha = password_hash($senha, PASSWORD_DEFAULT);


     //insercao : para fazer a insercao de dados no banco de dados, usamos o prepare, e buscamos o nome de conecxao no arquivo de conexao e de banco de dados.exemplo abaixo: 
    $sql = $conexao->prepare('INSERT INTO `login` SET nome = ?, tel = ?, email = ?, 
    endereco = ?, senha = ?, perfil = ?, cod = ?, estatus = ?, dataHora = ?');

    $array_sql = array(
        $nome, 
        $fone, 
        $email, 
        $endereco, 
        $senha, 
        $perfil, 
        $cod, 
        $estatus,
        $dataHora
        );

        // verifica se a execução da consulta SQL foi bem-sucedida. Se a execução for bem-sucedida, o código dentro do bloco if será executado, indicando que o cadastro foi realizado com sucesso. Caso contrário, o código dentro do bloco else será executado, indicando que houve uma falha no cadastro.;
    try {
        if($sql->execute($array_sql)){ 
            $resposta['erro'] = 'nao';
            $resposta['msg'] = 'Cadastro efectuado com sucesso';
        }else{
            $resposta['erro'] = 'sim';
            $resposta['getErro'] = 'Falha ao efectuar cadastro. Por favor, tente novamente';
        }
    } catch (PDOException $e) {
        $resposta['erro'] = 'sim';
        $resposta['getErro'] = 'Falha ao efectuar cadastro. Por favor, tente novamente';
    }
    echo json_encode($resposta);
}
?>
