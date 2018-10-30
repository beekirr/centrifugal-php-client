<?php

namespace CentrifugalClient\HashGenerator;

interface HashGeneratorInterface
{
    public function generate(array $data): string;
}
