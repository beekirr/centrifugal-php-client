<?php

namespace Centrifuge\HashGenerator;

interface HashGeneratorInterface
{
    public function generate(array $data): string;
}
