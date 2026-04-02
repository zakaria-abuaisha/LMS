<?php

namespace App\Enums;

enum ExaminationType: string
{
    case Quiz = 'quiz';
    case Mid = 'mid';
    case Final = 'final';
}
