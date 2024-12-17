<?php

class BancoSantander
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;
    private string $accessToken;

    private string $tokenPath;

    private string $certKeyFile;
    private string $certKeyPassword;

    private string $certFile;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['baseUrl'] ?? throw new InvalidArgumentException('baseUrl é obrigatório.');

        $this->clientId = $config['clientId'] ?? throw new InvalidArgumentException('clientId é obrigatório.');
        $this->clientSecret = $config['clientSecret'] ?? throw new InvalidArgumentException('clientSecret é obrigatório.');

        $this->tokenPath = $config['tokenPath'] ?? throw new InvalidArgumentException('tokenPath (Caminho para armazenar o token) é obrigatório.');

        $this->certKeyFile = $config['certKeyFile'] ?? throw new InvalidArgumentException('certKeyFile (Chave Privada) é obrigatória.');
        $this->certKeyPassword = $config['certKeyPassword'] ?? '';

        $this->certFile = $config['certFile'] ?? throw new InvalidArgumentException('certFile (Certificado) é obrigatório.');

        $this->accessToken =  $this->generateToken() ?? '';
    }


    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    // Função para recuperar o token do arquivo
    function generateToken()
    {
        if ($this->isTokenExpired()) {
            $this->authenticate();
            return $this->getAccessToken();
        }

        return file_get_contents($this->tokenPath);
    }

    // Função para verificar se token esta expirado
    function isTokenExpired()
    {
        if (!file_exists($this->tokenPath)) {
            return true; // Arquivo não existe, considera como expirado
        }
        // Verifica se o token foi modificado há mais de 12 minutos (720 segundos)
        return (time() - filemtime($this->tokenPath)) > 720;
    }


    /**
     * Autenticar na API / Recuperar ou solicitar Token
     */
    public function authenticate(): bool
    {
        $url = $this->baseUrl . '/auth/oauth/v2/token';

        $postData = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials'
        ];

        $response = $this->makeRequest('POST', $url, $postData);

        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            file_put_contents($this->tokenPath, $response['access_token']);
            return true;
        }

        return false;
    }

    /**
     * Create a workspace.
     */
    public function createWorkspace(array $workspaceData): array
    {
        $url = $this->baseUrl . '/collection_bill_management/v2/workspaces';
        return $this->makeRequest('POST', $url, $workspaceData, true);
    }

    /**
     * Get all workspaces.
     */
    public function getWorkspaces(): array
    {
        $url = $this->baseUrl . '/collection_bill_management/v2/workspaces';
        return $this->makeRequest('GET', $url, [], true);
    }

    /**
     * Get a workspace by ID.
     */
    public function getWorkspaceById(string $workspaceId): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/workspaces/$workspaceId";
        return $this->makeRequest('GET', $url, [], true);
    }

    /**
     * Generic method to make API requests.
     */
    private function makeRequest(string $method, string $url, array $data = [], bool $auth = false): array
    {
        $ch = curl_init();

        $headers = ['application/x-www-form-urlencoded'];
        $dataFields = http_build_query($data);

        if ($auth && $this->accessToken) {
            $headers = ['Content-Type: application/json'];
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
            $headers[] = 'X-Application-Key: ' . $this->clientId;
            $dataFields = json_encode($data);
        }

        if ($method === 'POST' || $method === 'PATCH') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataFields);
        }

        if ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }

        // Configurações de certificado
        curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verifica o certificado SSL
        curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile); // Define o certificado
        curl_setopt($ch, CURLOPT_SSLKEY, $this->certKeyFile); // Define a chave privada

        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $this->certKeyPassword);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        return ['error' => 'Request failed', 'status_code' => $httpCode, 'response' => $response];
    }


    /**
     * Register a bank slip (boleto/pix).
     */
    public function registerBankSlip(string $workspaceId, array $bankSlipData): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/workspaces/$workspaceId/bank_slips";
        return $this->makeRequest('POST', $url, $bankSlipData, true);
    }

    /**
     * Get all bank slips for a workspace.
     */
    public function getBankSlips(string $workspaceId, array $queryParams): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/workspaces/$workspaceId/bank_slips?" . http_build_query($queryParams);;
        return $this->makeRequest('GET', $url, [], true);
    }

    /**
     * Get a bank slip by ID.
     */
    public function getBankSlipById(string $workspaceId, string $bankSlipId): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/workspaces/$workspaceId/bank_slips/$bankSlipId";
        return $this->makeRequest('GET', $url, [], true);
    }

    /**
     * Send instructions for a bank slip.
     */
    public function sendBankSlipInstructions(string $workspaceId, array $instructionData): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/workspaces/$workspaceId/bank_slips";
        return $this->makeRequest('PATCH', $url, $instructionData, true);
    }

    /**
     * Get detailed bills information with filters.
     */
    public function getDetailedBills(array $queryParams): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/bills?" . http_build_query($queryParams);
        return $this->makeRequest('GET', $url, [], true);
    }

    /**
     * Generate a PDF for a bank slip.
     */
    public function generateBankSlipPDF(string $billId, array $bankSlipData): array
    {
        $url = $this->baseUrl . "/collection_bill_management/v2/bills/$billId/bank_slips";
        return $this->makeRequest('POST', $url, $bankSlipData, true);
    }
}
