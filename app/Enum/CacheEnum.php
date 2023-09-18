<?php

namespace App\Enum;

enum CacheEnum: int
{
    const ONE_HOUR = 3600;
    const TWELVE_HOURS = 43200;
    const ONE_DAY = 86400; //24h
    const ONE_WEEK = 604800;
    const TWO_WEEK = (604800 * 2);
    const THREE_WEEK = (604800 * 3);
    const ONE_MONTH = (604800 * 4);
    const TWO_MONTHS = (604800 * 8);
    const THREE_MONTHS = (604800 * 12);
}
