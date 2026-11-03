<?php

    
// try catch é usado para capturar exceções que podem ocorrer durante a execução do código dentro do bloco try. Se ocorrer uma exceção, o fluxo de execução será transferido para o bloco catch, onde você pode lidar com o erro de forma apropriada.;
    try{
        $conexao = new PDO('mysql:host=localhost;dbname=ecomerce;charset=utf8mb4', 'root', '');
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){ // $e é uma variável que armazena a exceção lançada pelo PDO em caso de falha na conexão. A classe PDOException é usada para capturar erros relacionados ao PDO.;
        $erroConexao = $e->getMessage();
    }

?> 