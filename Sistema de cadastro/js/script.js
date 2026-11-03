var container = document.getElementById('container')
var container_cadastro = document.getElementById('container_cadastro')

function open_form_cadastro(){
    container.style.display = 'none'
    container_cadastro.style.display = 'flex'
}

function open_form_login(){
    container.style.display = 'flex'
    container_cadastro.style.display = 'none'
}

function cadastro(){
    let nome     = $('#nome_cadastro').val() //val() é um método do jQuery que retorna o valor atual do primeiro elemento selecionado. Neste caso, ele está sendo usado para obter o valor do campo de entrada com o ID "nome_cadastro", que é presumivelmente um campo de formulário onde o usuário insere seu nome durante o processo de cadastro.
    let fone     = $('#tel_cadastro').val()
    let email    = $('#email_cadastro').val()
    let endereco = $('#endereco_cadastro').val()
    let senha    = $('#senha_cadastro').val()
    let rsenha   = $('#rsenha_cadastro').val()

    if(nome === ''){
        $('#msgCadastro').html('<div class="erro">Preencha o seu nome</div>') //classe "erro" é usada para estilizar a mensagem de erro exibida ao usuário. 
    }else if(fone === ''){
        $('#msgCadastro').html('<div class="erro">Informe um número de telefone válido</div>')
    }else if(email === ''){
        $('#msgCadastro').html('<div class="erro">Informe um endereço de e-mail válido</div>')
    }else if(senha === ''){
        $('#msgCadastro').html('<div class="erro">Crié uma senha</div>')
    }else if(rsenha === ''){
        $('#msgCadastro').html('<div class="erro">Repete a senha</div>')
    }else if(rsenha !== senha){
        $('#msgCadastro').html('<div class="erro">A senhas não correspondem</div>')
    }else{
      

        $.ajax({
            type: 'POST',
            url: '/sistema-admin/php/cadastro.php',
            data:{
                    _nome     : $('#nome_cadastro').val(),
                    _fone     : $('#tel_cadastro').val(),
                    _email    : $('#email_cadastro').val(),
                    _endereco : $('#endereco_cadastro').val(),
                    _senha    : $('#senha_cadastro').val()
                },
            dataType: 'json', 
            beforeSend:function(){
                $('#msgCadastro').html('<div class="warning">Processando dados. Aguarde...</div>')// Mensagem de aviso enquanto processa os dados
            },
            // Função de sucesso que será executada quando a requisição AJAX for concluída com sucesso esta liagada ao arquivo cadastro.php, que é responsável por processar os dados do formulário de cadastro e retornar uma resposta em formato JSON. A função verifica se houve algum erro no processamento dos dados e exibe a mensagem correspondente na tela.
            success:function(valor){
                if(valor.erro =='sim'){
                    $('#msgCadastro').html('<div class="erro">' + valor.getErro + '</div>')
                }else{
                    $('#msgCadastro').html('<div class="sucesso">' + valor.msg + '</div>')


                    setTimeout(function(){ // A função setTimeout é usada para criar um atraso de 3 segundos antes de executar o código dentro dela. Isso permite que o usuário veja a mensagem de sucesso antes que o formulário seja resetado e a mensagem seja limpa.
                        document.getElementById('form_cadastro1').reset()
                        $('#msgCadastro').html('')
                        open_form_login()
                    }, 3000)
                }
            },
            error:function(xhr){
                if(xhr.status === 0){ //  xhr.status é uma propriedade do objeto XMLHttpRequest que representa o status da resposta HTTP recebida do servidor. O valor 0 indica que a requisição não conseguiu se conectar ao servidor, o que pode ocorrer por vários motivos, como problemas de rede, servidor offline ou bloqueio de firewall. Neste caso, a mensagem sugere que o usuário abra o sistema localmente e verifique se o Apache está ativo no XAMPP.
                    $('#msgCadastro').html('<div class="erro">Abra o sistema por http://localhost/sistema-admin/ e deixe o Apache ativo no XAMPP.</div>')//o que serve o apache no xampp é para criar um servidor local, que permite que você execute aplicativos web em seu próprio computador. Ele fornece um ambiente de servidor web completo, incluindo o servidor Apache, o interpretador PHP e o banco de dados MySQL. 
                }else{
                    $('#msgCadastro').html('<div class="erro">Erro HTTP ' + xhr.status + ' ao comunicar com o servidor.</div>')
                }
            }
        })
    }
} 

// login

function login(){
    let user = $('#user').val()
    let pass = $('#pass').val()

    // Validação dos campos de login
    if(user === ''){
        $('#msgLogin').html('<div class="erro">Preencha o seu e-mail</div>')
    }else if(pass === ''){
        $('#msgLogin').html('<div class="erro">Preencha a sua senha</div>')
    }else{
        // Requisição AJAX para o arquivo login.php, que é responsável por processar os dados de login e retornar uma resposta em formato JSON. A função verifica se houve algum erro no processamento dos dados e exibe a mensagem correspondente na tela.
        $.ajax({
            type: 'POST',
            url: 'php/login.php',
            data: {
                user: user,
                pass: pass
            },
            dataType: 'json',
            beforeSend: function(){
                $('#msgLogin').html('<div class="warning">Processando dados. Aguarde...</div>')
            },


            success: function(valor){
                if(valor.erro === 'sim'){
                    $('#msgLogin').html('<div class="erro">' + valor.getErro + '</div>')
                }else{
                    $('#msgLogin').html('<div class="sucesso">' + valor.msg + '</div>')

                    setTimeout(function(){
                        if(valor.redirect){
                            window.location.href = valor.redirect
                        }
                    }, 3000)
                }
            },
            error: function(xhr){
                $('#msgLogin').html('<div class="erro">Erro HTTP ' + xhr.status + ' ao comunicar com o servidor.</div>')
            }
        })
    }
}
