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

- **Certificado digital (arquivo `.crt` e chave privada `.key`)**: Necessários para autenticação na API.
- **Credenciais de API**: Incluem `client_id` e `client_secret`, obtidos no painel de desenvolvedores do Banco Santander.

## 🚀 Exemplos de Uso
```php
require 'BancoSantander.class.php';
$bancoSantander = new BancoSantander();
```

### Geração de cobrança (Boleto/PIX)
```php
$params = [
    "seuNumero" => "1",
    "valorNominal" => 2.5,
    "dataVencimento" => '2025-01-18',
    "numDiasAgenda" => 60,
    "pagador" => [
        "email" => "nome.sobrenome@xis-domain.com.br",
        "ddd" => "38",
        "telefone" => "999999999",
        "numero" => "3456",
        "complemento" => "apartamento 3 bloco 4",
        "cpfCnpj" => "11122233344",
        "tipoPessoa" => "FISICA",
        "nome" => "Diogo Dourado",
        "endereco" => "Avenida da Felicidad, 123456",
        "bairro" => "Centro",
        "cidade" => "Montes Claros",
        "uf" => "MG",
        "cep" => "39400000"
    ]
];
$codigoSolicitacao = $bancoSantander->cobrancaSet($params);
print_r($codigoSolicitacao);
```

### Consulta cobrança (Boleto/PIX)
```php
$cobranca = $bancoSantander->cobrancaGet($codigoSolicitacao);
print_r($$cobranca);
```

### Recuperando cobrança em PDF (Boleto/PIX)
```php
$pdf_base64 = $bancoSantander->cobrancaGetPdf($codigoSolicitacao);
```

### Cancelando cobrança (Boleto/PIX)
```php
$result = $bancoSantander->cobrancaCancel($codigoSolicitacao, 'Motivo do cancelamento aqui.');
print_r($result);
```

### Listar cobranças (Boleto/PIX)
```php
$params = [
    'dataInicial' => '2024-12-01',
    'dataFinal' => '2024-12-20',
    'filtrarDataPor' => 'EMISSAO',
    'situacao' => NULL,
    'pessoaPagadora' => NULL,
    'cpfCnpjPessoaPagadora' => NULL,
    'seuNumero' => NULL,
    'paginacao' => NULL,
    'ordenarPor' => NULL,
    'tipoOrdenacao' => NULL,
];
$codigoSolicitacao = $bancoSantander->cobrancaList($params);
print_r($codigoSolicitacao);
```

## 📝 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).


## 💰 Contribua com o Desenvolvimento

Se este código foi útil para você e deseja contribuir como forma de agradecimento, pode enviar qualquer valor para meu PIX: **diogo@dourado.net**. Toda contribuição é muito bem-vinda! 🎉
