<?php
    class Criteo_HTTP_Client {

        protected function buildQuery($url, $data = [], $delim = '?'){
            return $url . $delim . http_build_query($data);
        }

        protected function httpRequest($req){
            $ch = curl_init();
            $url = "{$req['protocol']}://{$req['host']}{$req['path']}";
            $response_headers = [];

            if ($req['method'] === 'GET'){
                $url = $this->buildQuery($url, $req['body']);
            }else{
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $req['body']);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req['method']);
            }

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $req['headers'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$response_headers){
                    $length = strlen($header);
                    $header = explode(':', $header, 2);
                    if (count($header) === 2){
                        $response_headers[$header[0]] = $header[1];
                    }
                    return $length;
                }
            ]);

            $response_body = curl_exec($ch);
            $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $error = curl_error($ch);

            curl_close($ch);

            if ($error){
                throw new Exception($error, $response_code);
            }

            return [
                'code' => $response_code,
                'headers' => $response_headers,
                'body' => $response_body
            ];
        }
    }
?>
