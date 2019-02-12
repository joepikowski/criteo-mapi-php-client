<?php
    class Criteo_API_Client extends Criteo_HTTP_Client {
        protected $host;
        protected $endpoint;

        public function __construct($host = '', $endpoint = ''){
            $this->host = $host;
            $this->endpoint = $endpoint;
        }

        public function apiGET($req){
            return $this->apiRequest('GET', $req);
        }

        public function apiPOST($req){
            return $this->apiRequest('POST', $req);
        }

        public function apiDELETE($req){
            return $this->apiRequest('DELETE', $req);
        }

        public function apiPUT($req){
            return $this->apiRequest('PUT', $req);
        }

        public function apiPATCH($req){
            return $this->apiRequest('PATCH', $req);
        }

        public function apiRequest($method, $req){
            return $this->httpRequest([
                'method' => $method,
                'host' => $this->host,
                'protocol' => $req['protocol'] ?? 'https',
                'path' => $this->endpoint . ( $req['path'] ?? '/' ),
                'headers' => $req['headers'] ?? [],
                'body' => $req['body']
            ]);
        }
    }
?>
