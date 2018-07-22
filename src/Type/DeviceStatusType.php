<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/22
 * Time: 10:12 AM
 */

namespace App\Type;


class DeviceStatusType
{
    const INVALID = -1;
    const SERVER_ERROR = 0;
    const NORMAL = 1;
    const SENT = 2;
    const ACKNOWLEDGED = 3;
    const VIEWED = 4;
}