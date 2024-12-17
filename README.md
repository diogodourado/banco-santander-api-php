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
Em todas as funções, o código já checka o token de autenticação, se ele existe e se ele é valido, se não, gera um novo. Isso evita de gerar o token a cada requisição, fazendo o reaproveitamento do mesmo.

```php
require 'BancoSantander.class.php';
$bancoSantander = new BancoSantander([
    'baseUrl' => 'https://trust-open.api.santander.com.br',
    'clientId' => 'Seu Client ID',
    'clientSecret' => 'Seu Client Secret',
    'tokenPath' => 'Caminho para armazenar o token',
    'certKeyFile' => 'Caminho para o arquivo da chave privada',
    'certKeyPassword' => 'Senha da chave privada (opcional)',
    'certFile' => 'Caminho para o arquivo do certificado',
]);
```


### O primeiro passo é a criaçao do Workspace
O Workspace é onde você define qual o convênio usado e configura a sua URL do Webhook para retorno.
```php
$paramsWorkspace = [
        'type' => 'BILLING',
        'description' => 'Workspace de Cobrança',
        'covenants' => [['code' => '1234567']], // Número do convenio (solicite o seu Gerente)
        'webhookURL' => 'https://seu-dominio.com/santander-webhook/',
        'bankSlipBillingWebhookActive' => true,
        'pixBillingWebhookActive' => true
    ];
$resultWorkspace = $bancoSantander->createWorkspace($paramsWorkspace);
print_r($resultWorkspace);
```

### Depois ja é possivel registrar as cobranças (boleto / pix)
Para registrar a cobrança, você ira precisar do ID do Workspace criado anteriormente
```php
$paramsRegister = [
    "environment" => "PROCUCAO",
    "nsuCode" => 12345678901234567000,
    "nsuDate" => "2022-12-12",
    "covenantCode" => "1234567",
    "bankNumber" => "123",
    "clientNumber" => "fd119Dc4d48F460",
    "dueDate" => "2022-12-12",
    "issueDate" => "2022-12-12",
    "participantCode" => "registro1234567890",
    "nominalValue" => "10.15",
    "payer" => [
        "name" => "João da Silva",
        "documentType" => "CPF",
        "documentNumber" => 9615865832,
        "address" => "Rua XV de Maio",
        "neighborhood" => "Vila Industrial",
        "city" => "São Paulo",
        "state" => "SP",
        "zipCode" => "09761-233"
    ],
    "beneficiary" => [
        "name" => "João da Silva",
        "documentType" => "CPF",
        "documentNumber" => 9615865832
    ],
    "documentKind" => "DUPLICATA_MERCANTIL",
    "discount" => [
        "type" => "VALOR_DATA_FIXA",
        "discountOne" => [
            "value" => 5.5,
            "limitDate" => "2022-12-12"
        ],
        "discountTwo" => [
            "value" => 5.5,
            "limitDate" => "2022-12-12"
        ],
        "discountThree" => [
            "value" => 5.5,
            "limitDate" => "2022-12-12"
        ]
    ],
    "finePercentage" => "97.80",
    "fineQuantityDays" => "5",
    "interestPercentage" => "5.00",
    "deductionValue" => "10.00",
    "protestType" => "SEM_PROTESTO",
    "protestQuantityDays" => "32",
    "writeOffQuantityDays" => "32",
    "paymentType" => "REGISTRO",
    "parcelsQuantity" => "32",
    "valueType" => "PERCENTUAL",
    "minValueOrPercentage" => "32.06",
    "maxValueOrPercentage" => "49.36",
    "iofPercentage" => "32.45325",
    "sharing" => [
        [
            "code" => "12",
            "value" => "132.5"
        ]
    ],
    "key" => [
        "type" => "CPF",
        "dictKey" => "09463589723"
    ],
    "txId" => "1234567890abcdefghij123456",
    "messages" => [
        "mensagem um",
        "mensagem dois"
    ]
];
$resultBankSlip = $bancoSantander->registerBankSlip($santander_workspace_id, $paramsRegister);
print_r($resultBankSlip);
```

## Funções Disponíveis
Para saber quais parametros passar em cada função, consulte a [documentação oficial do Banco Santander](https://developer.santander.com.br/api/documentacao/emissao-de-boletos-visao-geral#/).

### Autenticação
- **`getAccessToken()`**  
  Retorna o token de acesso atual.

- **`generateToken()`**  
  Recupera ou solicita um novo token de acesso.

- **`isTokenExpired()`**  
  Verifica se o token está expirado.

- **`authenticate()`**  
  Realiza a autenticação na API e armazena o token.

---

### Workspaces
- **`createWorkspace(array $workspaceData)`**  
  Cria um novo workspace.

- **`getWorkspaces()`**  
  Retorna todos os workspaces.

- **`getWorkspaceById(string $workspaceId)`**  
  Retorna informações de um workspace específico pelo ID.

---

### Boletos Bancários
- **`registerBankSlip(string $workspaceId, array $bankSlipData)`**  
  Registra um boleto bancário ou PIX em um workspace.

- **`getBankSlips(string $workspaceId, array $queryParams)`**  
  Retorna todos os boletos de um workspace, com filtros opcionais.

- **`getBankSlipById(string $workspaceId, string $bankSlipId)`**  
  Retorna informações detalhadas de um boleto bancário específico.

- **`sendBankSlipInstructions(string $workspaceId, array $instructionData)`**  
  Envia instruções para um boleto bancário.

---

### Contas Detalhadas
- **`getDetailedBills(array $queryParams)`**  
  Retorna informações detalhadas de contas com base em filtros.

- **`generateBankSlipPDF(string $billId, array $bankSlipData)`**  
  Gera um PDF para um boleto bancário.

---

### Métodos Internos
- **`makeRequest(string $method, string $url, array $data = [], bool $auth = false)`**  
  Realiza chamadas genéricas à API utilizando `cURL`.

---

## Requisitos
- PHP 7.4+
- Extensões `cURL` e `openssl` ativas.

---

## 📝 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

## 💰 Contribua com o Desenvolvimento

Se este código foi útil para você e deseja contribuir como forma de agradecimento, pode enviar qualquer valor para meu PIX: **diogo@dourado.net**. Toda contribuição é muito bem-vinda! 🎉
