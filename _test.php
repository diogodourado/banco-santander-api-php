<?

$santanderAPI = new SantanderAPI([
    'baseUrl' => 'https://trust-open.api.santander.com.br',

    'clientId' => 'seuClientId',
    'clientSecret' => 'seuClientSecret',

    'tokenPath' => '/path/para/token',

    'certKeyFile' => '/path/para/certkey.pem',
    'certKeyPassword' => 'suaSenha',

    'certFile' => '/path/para/cert.pem',
]);
