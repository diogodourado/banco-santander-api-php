<?
$bancoSantander = new BancoSantander([
    'baseUrl' => 'https://trust-open.api.santander.com.br',

    'clientId' => 'seuClientId',
    'clientSecret' => 'seuClientSecret',

    'tokenPath' => '/path/para/token',

    'certKeyFile' => '/path/para/certkey.pem',
    'certKeyPassword' => 'suaSenha',

    'certFile' => '/path/para/cert.pem',
]);


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
