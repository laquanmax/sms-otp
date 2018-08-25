<?php

namespace SMSOTP;

use SMSOTP\Contract\Generator;

class OTPCode implements Generator
{
    public function generate($length = 6) : string
    {
        return strtoupper(substr(hash('sha256', strtotime('now')), 0, $length));
    }
}
