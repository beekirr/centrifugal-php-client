<?php

namespace Centrifuge\HashGenerator;

class HashGenerator implements HashGeneratorInterface
{
    private const DEFAULT_ALGO = 'sha256';
    private const DEFAULT_SECRET = 'secret';

    /**
     * @var string
     */
    private $algo;
    private $secret;

    public function __construct(
        $secret = self::DEFAULT_SECRET,
        $algo = self::DEFAULT_ALGO
    ) {
        $this->algo = $algo;
        $this->secret = $secret;
    }

    /**
     * @param array $data
     * @return string
     */
    public function generate(array $data): string
    {
        $context = hash_init($this->algo, HASH_HMAC, $this->secret);

        foreach ($data as $row){
            hash_update($context, $row);
        }

        return hash_final($context);
    }
}