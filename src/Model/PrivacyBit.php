<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/4
 * Time: 9:52 PM
 */

namespace App\Model;


class PrivacyBit
{
    const GENERAL = 0;
    const CONTACT = 1;
    const PHONE_OR_EMAIL = 2;

    const ALL = [self::GENERAL, self::CONTACT, self::PHONE_OR_EMAIL];
}