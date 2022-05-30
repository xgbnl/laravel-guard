<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Enum;

enum Date: int
{
    // 两小时
    case TWO_HOURS = 7200;

    // 四小时
    case FOUR_HOURS = 14400;

    // 两天
    case TWO_DAYS = 172800;
}
