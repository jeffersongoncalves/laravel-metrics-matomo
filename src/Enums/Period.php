<?php

namespace JeffersonGoncalves\MetricsMatomo\Enums;

enum Period: string
{
    case Day = 'day';
    case Week = 'week';
    case Month = 'month';
    case Year = 'year';
    case Range = 'range';
}
