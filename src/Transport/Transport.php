<?php

namespace Centrifuge\Transport;

class Transport implements TransportInterface
{
    /**
     * @var string|null Certificate file name
     */
    private $cert;
    /**
     * @var string|null Directory containing CA certificates
     */
    private $caPath;

    /**
     * @var int|null
     */
    private $connectTimeoutOption;

    /**
     * @var int|null
     */
    private $timeoutOption;

    /**
     *
     * @param string $host
     * @param array $data
     * @return mixed
     * @throws TransportException
     */
    public function communicate(string $host, array $data)
    {
        $ch = curl_init("$host/api/");

        if (!$ch) {
            throw new TransportException('CURL init failure');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        if ($this->connectTimeoutOption !== null) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeoutOption);
        }
        if ($this->timeoutOption !== null) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutOption);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if (null !== $this->cert) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->cert);
        }
        if (null !== $this->caPath) {
            curl_setopt($ch, CURLOPT_CAPATH, $this->caPath);
        }

        $postData = http_build_query($data, '', '&');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        /** @var string $response */
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        if (empty($headers['http_code']) || ($headers['http_code'] !== 200)) {
            throw new TransportException(
                'Response code: '
                . $headers['http_code']
                . PHP_EOL
                . 'cURL error: ' . $error . PHP_EOL
                . 'Body: '
                . $response
            );
        }

        return json_decode($response, true);
    }

    /**
     * @return string|null
     * @since 1.0.5
     */
    public function getCert(): ?string
    {
        return $this->cert;
    }

    /**
     * @param string|null $cert
     * @since 1.0.5
     */
    public function setCert($cert): void
    {
        $this->cert = $cert;
    }

    /**
     * @return string|null
     * @since 1.0.5
     */
    public function getCAPath(): ?string
    {
        return $this->caPath;
    }

    /**
     * @param string|null $caPath
     * @since 1.0.5
     */
    public function setCAPath($caPath): void
    {
        $this->caPath = $caPath;
    }

    /**
     * @return int|null
     */
    public function getConnectTimeoutOption(): ?int
    {
        return $this->connectTimeoutOption;
    }

    /**
     * @return int|null
     */
    public function getTimeoutOption(): ?int
    {
        return $this->timeoutOption;
    }

    /**
     * @param int|null $connectTimeoutOption
     */
    public function setConnectTimeoutOption($connectTimeoutOption): void
    {
        $this->connectTimeoutOption = $connectTimeoutOption;
    }

    /**
     * @param int|null $timeoutOption
     */
    public function setTimeoutOption($timeoutOption): void
    {
        $this->timeoutOption = $timeoutOption;
    }
}
