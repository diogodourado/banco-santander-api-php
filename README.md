# Banco Santander API - PHP

Uma implementação em PHP para integrar com a API do Banco Santander, permitindo a realização de operações financeiras como geração de boletos e pix.

## 📋 Funcionalidades

- Suporte a autenticação via Baren Token. (Certificado / Token)
- Geração, consulta e cancelamento de Workspace / Webhook.
- Geração, consulta e cancelamento de Cobranças (Boleto/PIX).

### Referência Oficial da API

Para configurar cobranças via Boleto ou PIX, consulte a [documentação oficial do Banco Santander](https://developer.santander.com.br/api/documentacao/emissao-de-boletos-visao-geral#/). Lá você encontrará os parâmetros necessários, exemplos de requisições e explicações sobre as respostas e erros. 

## 📦 Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/diogodourado/banco-santander-api-php.git
   ```
2. Inclua o arquivo `BancoSantander.class.php` no seu projeto.

## ⚙️ Configuração

Certifique-se de configurar os seguintes parâmetros antes de utilizar a classe:

- **Certificado digital (sendo dois arquivos `.pem`, um da chave privada e outro da chave pública)**: Necessários para autenticação na API.
- **Credenciais de API**: Incluem `client_id` e `client_secret`, obtidos no painel de desenvolvedores do Banco Santander.

Caso tenha alguma dúvida de como converter o seu certificado, escrevi um post no meu blog:
[dourado.net - Conversão de Certificados .p12 para .pem na Integração de APIs do Banco Santander com PHP](https://dourado.net/2024/12/16/conversao-de-certificados-p12-para-pem-na-integracao-de-apis-do-banco-santander-com-php/)

## 🚀 Exemplos de Uso
```php
require 'BancoSantander.class.php';
$bancoSantander = new BancoSantander();
$santanderAPI = new SantanderAPI([
    'baseUrl' => 'https://trust-open.api.santander.com.br',

    'clientId' => 'seuClientId',
    'clientSecret' => 'seuClientSecret',

    'tokenPath' => '/path/para/token',

    'certKeyFile' => '/path/para/certkey.pem',
    'certKeyPassword' => 'suaSenha',

    'certFile' => '/path/para/cert.pem',
]);
```

Em todas as funções, o código já checka o token de autenticação, se ele existe e se ele é valido, se não, gera um novo. Isso evita de gerar o token a cada requisição, fazendo o reaproveitamento do mesmo.

### O primeiro passo é a criaçao do Workspace
O Workspace é onde você define qual o convênio usado e configura a sua URL do Webhook para retorno.
```php
$params = [
    'aa' => 'bbb',
];
$result = $bancoSantander->cmd($params);
print_r($result);
```

### Depois ja é possivel registrar as cobranças (boleto / pix)
Para registrar a cobrança, você ira precisar do ID do Workspace criado anteriormente
```php
$params = [
    'aa' => 'bbb',
];
$result = $bancoSantander->cmd($params);
print_r($result);
```

## 📝 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).


## 💰 Contribua com o Desenvolvimento

Se este código foi útil para você e deseja contribuir como forma de agradecimento, pode enviar qualquer valor para meu PIX: **diogo@dourado.net**. Toda contribuição é muito bem-vinda! 🎉
