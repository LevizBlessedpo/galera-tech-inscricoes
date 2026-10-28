<?php

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja bem-vindo!</title>
</head>

<body>
    <h1>Inscrições Galera Tech</h1>

    <br>

    <form class="form-container" method="post">
        <div id="container_principal">
            <div id="aluno_container">
                <h2>01 - Dados pessoais do candidato</h2>

                <div>
                    <label for="nome_completo_aluno">Nome completo:</label>
                    <input type="text" id="nome_completo_aluno" class="nome_aluno" name="nome_aluno" required>
                </div>

                <br>

                <div>
                    <label for="data_nascimento_aluno">Data de nascimento:</label>
                    <input type="date" id="data_nascimento_aluno" class="dataNascimento_aluno" name="dataNascimento_aluno" required>
                </div>

                <br>

                <div>
                    <label for="cpf_aluno">CPF do candidato:</label>
                    <input type="text" id="cpf_aluno" class="cpf_aluno" name="cpf_aluno" placeholder="000.000.000-00" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" maxlength="14" required>
                </div>

                <br>

                <div>
                    <label for="rg_aluno">RG do candidato ou CIN:</label>
                    <input type="text" id="rg_aluno" class="rg_aluno" name="rg_aluno" placeholder="00.000.000-0 ou CPF" required>
                </div>

                <br>

                <div>
                    <label for="sexo_aluno">Sexo do candidato:</label>
                    <select id="sexo_aluno" name="sexo_aluno" required>
                        <option value="">Selecione</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                        <option value="Não desejo informar">Não desejo informar</option>
                    </select>
                </div>

                <br>

                <div>
                    <label for="email_aluno">E-mail do candidato:</label>
                    <input type="email" id="email_aluno" class="email_aluno" name="email_aluno" required>
                </div>

                <br>

                <div>
                    <label for="telefone_aluno">Telefone do candidato:</label>
                    <input type="tel" id="telefone_aluno" class="telefone_aluno" name="telefone_aluno" placeholder="(00) 00000-0000" required>
                </div>

            </div>

            <div id="endereco_container">

                <h2>02 - Endereço do candidato</h2>

                <br>

                <div>
                    <label for="cep_aluno">CEP:</label>
                    <input type="text" id="cep_aluno" class="cep_aluno" name="cep_aluno" maxlength="9" placeholder="00000-000" required>
                </div>

                <br>

                <div>
                    <label for="logradouro_aluno">Logradouro do aluno:</label>
                    <input type="text" id="logradouro_aluno" class="logradouro_aluno" name="logradouro_aluno" required>
                </div>

                <br>

                <div>
                    <label for="numero_residencia_aluno">Número da residência:</label>
                    <input type="text" id="numero_residencia_aluno" class="numero" name="numero" required>
                </div>

                <br>

                <div>
                    <label for="complemento_aluno">Complemento:</label>
                    <input type="text" id="complemento_aluno" class="complemento" name="complemento">
                </div>

                <br>

                <div>
                    <label for="bairro_aluno">Bairro:</label>
                    <input type="text" id="bairro_aluno" class="bairro" name="bairro" required>
                </div>

                <br>

                <div>
                    <label for="cidade_aluno">Cidade:</label>
                    <input type="text" id="cidade_aluno" class="cidade" name="cidade" required>
                </div>

                <br>

                <div>
                    <label for="estado_aluno">Estado (UF):</label>
                    <input type="text" id="estado_aluno" class="estado" name="estado" placeholder="Ex.: SP" maxlength="2" required>
                </div>

                <br>

                <div>
                    <label for="regiao_aluno">Região:</label>
                    <select id="regiao_aluno" name="regiao" required>
                        <option value="">Selecione</option>
                        <option value="Norte">Norte</option>
                        <option value="Sul">Sul</option>
                        <option value="Leste">Leste</option>
                        <option value="Oeste">Oeste</option>
                        <option value="Centro">Centro</option>
                    </select>
                </div>

            </div>

        </div>

    </form>
</body>

</html>