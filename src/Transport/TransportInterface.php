<?php

namespace CentrifugalClient\Transport;

interface TransportInterface
{
    /**
     * @param string $host
     * @param array $data
     * @return mixed
     */
    public function communicate(string $host, array $data);
} 