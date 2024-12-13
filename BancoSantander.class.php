<?php
class BancoSantander
{
    private $contaCorrente, $clientId, $clientSecret, $certPath, $keyPath, $tokenPath, $baseUrl, $scope, $cpfCnpj;

    public function __construct(string $type = '')
    {
        include 'config.php';
    }



    function getBearerToken()
    {
        if (!file_exists($this->tokenPath) || file_exists($this->tokenPath) && (time() - filemtime($this->tokenPath) > 3000))
            return $this->generateBearerToken();

        return file_get_contents($this->tokenPath);
    }

    function generateBearerToken()
    {

        $response = $this->request('POST', 'oauth/v2/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ], true);

        file_put_contents($this->tokenPath, $response['access_token']);

        return $response['access_token'];
    }

    function request(string $method, string $endpoint, array $data = [], bool $noAuth = false)
    {
        if ($noAuth === false)
            $BearerToken = $this->getBearerToken();

        $ch = curl_init();
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $method = strtoupper($method);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $this->certPath,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $headers = $noAuth === true ? array(
            'Content-Type: application/x-www-form-urlencoded',
            "client_id: {$this->clientId}",
            "client_secret: {$this->clientSecret}"
        ) : array('Authorization: Bearer ' . $BearerToken, 'x-conta-corrente: ' . $this->contaCorrente, 'Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $noAuth === true ? http_build_query($data) : json_encode($data));
        } elseif (in_array($method, ['PUT', 'DELETE'], true)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $method === 'DELETE' ? http_build_query($data) : json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $serverResponse = curl_exec($ch);

        if ($serverResponse === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Erro cURL: $error");
        }

        curl_close($ch);

        $response = json_decode($serverResponse, true);

        if ($serverResponse != '' && (json_last_error() !== JSON_ERROR_NONE)) {
            $serverResponseJson = json_encode($serverResponse);
            throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg() . " - Mensagem recebida:" . $serverResponseJson);
        }

        return $response;
    }
}
