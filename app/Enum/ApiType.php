<?php

namespace App\Enum;

enum ApiType: int
{
    case NEWSAPI = 1;
    case NYTIMES = 2;
    case OPENNEWS = 3;
}
