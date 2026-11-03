<?php
    // Lógica de login
    //include a conexao
    include_once('conexao.php');
   
    $resposta = [];
    //verificar se hove envio de dados via POST

    if(isset($_POST['user']) && isset($_POST['pass'])){
        $user = $_POST['user'];
        $pass = $_POST['pass'];

        //validar se o usuario e senha estao corretos
        
        try {
            //vericar se o usuario e senha existem no banco de dados
            $consulta = $conexao->prepare("SELECT * FROM login WHERE email = :user");
            $consulta->bindParam(':user', $user); //bindParam é um método usado para vincular o valor da variável $user ao parâmetro :user na consulta SQL preparada. Isso ajuda a prevenir ataques de SQL injection, garantindo que o valor do usuário seja tratado de forma segura.;
            $consulta->execute();

            if($consulta->rowCount() == 1){
                $valor = $consulta->fetch(PDO::FETCH_ASSOC);//pega os dados da consulta em valores

                //passar os dados do banco de dados para variaveis
                $nomeBanco = $valor['nome'];
                $senhaBanco = $valor['senha'];
                $perfilBanco = $valor['perfil'];

                //verificar se a senha informada pelo usuario é igual a senha do banco de dados
                if($pass_verificada = password_verify($pass, $senhaBanco)) {
                    //senha correta
                    $resposta['erro'] = 'nao';
                    $resposta['msg'] = 'Login realizado com sucesso.';
                    $resposta['nome'] = $nomeBanco;
                    $resposta['perfil'] = $perfilBanco;

                    //Definir o redirecionamento com base no perfil do usuário
                    switch ($perfilBanco) {
                        case 'admin':
                            $resposta['redirect'] = 'admin.html';
                            break;
                        case 'comercial':
                            $resposta['redirect'] = 'admin/comercial.php';
                            break;
                        case 'caixa':
                            $resposta['redirect'] = 'admin/caixa.php';
                            break;
                        case 'cliente':
                            $resposta['redirect'] = 'admin/cliente.php';
                            break;
                        case 'Usuário':
                            $resposta['redirect'] = 'index.html';
                            break;
                        default:
                            $resposta['erro'] = 'sim';
                            $resposta['getErro'] = 'perfil invalido';
                            break;
                    }

                    echo json_encode($resposta);
                    exit;
                }else{
                    $resposta['erro'] = 'sim';
                    $resposta['getErro'] = 'Senha incorreta.';
                    echo json_encode($resposta);
                    exit;
                }
                   

            }

            $resposta['erro'] = 'sim';
            $resposta['getErro'] = 'E-mail ou senha incorretos.';
            echo json_encode($resposta);
            exit;

        }catch (PDOException $e) {
            $resposta['erro'] = 'sim';
            $resposta['getErro'] = 'Erro ao conectar ao banco de dados: ' . $e->getMessage();//getMessage() é um método que retorna a mensagem de erro associada à exceção lançada. Neste caso, ele está sendo usado para capturar e exibir a mensagem de erro específica que ocorreu durante a tentativa de conexão ao banco de dados. Isso ajuda a identificar problemas de conexão e fornece informações úteis para depuração.; 
            echo json_encode($resposta);
            exit;
        }

    }
?>        