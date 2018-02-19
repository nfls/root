<?php

namespace App\Service\Notification;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Predis\Client;

/**
 * Class AbstractSmsService
 * @package App\Service\Notification
 */
abstract class AbstractNotificationService
{

    /**
     * @var $util PhoneNumberUtil
     */
    protected $util;

    /**
     * AbstractSmsService constructor.
     */
    public function __construct()
    {
        $this->util = PhoneNumberUtil::getInstance();
    }

    /**
     * @param PhoneNumber $phone
     * @return string
     */
    protected function getDomesticNumber(PhoneNumber $phone)
    {
        return $this->util->format($phone, PhoneNumberFormat::NATIONAL);
    }

    /**
     * @param PhoneNumber $phone
     * @return string
     */
    protected function getInternationalNumber(PhoneNumber $phone)
    {
        return $this->util->format($phone, PhoneNumberFormat::E164);
    }

    /**
     * @param PhoneNumber $phone
     * @return string
     */
    abstract public function sendRegistration(PhoneNumber $phone);

    /**
     * @param PhoneNumber $phone
     * @return string
     */
    abstract public function sendBind(PhoneNumber $phone);

    /**
     * @param PhoneNumber $phone
     * @return string
     */
    abstract public function sendReset(PhoneNumber $phone);

    /**
     * @param PhoneNumber $phone
     * @return null
     */
    abstract public function sendRealnameFailed(PhoneNumber $phone);

    /**
     * @param PhoneNumber $phone
     * @param string $status
     * @param string $expiry
     * @return null
     */
    abstract public function sendRealnameSucceeded(PhoneNumber $phone, string $status, string $expiry);

    /**
     * @param PhoneNumber $phone
     * @return null
     */
    abstract public function sendNewMessage(PhoneNumber $phone);

    /**
     * @param PhoneNumber $phone
     * @param string $teacher
     * @param string $class
     * @return null
     */
    abstract public function sendNewNotice(PhoneNumber $phone, string $teacher, string $class);

    /**
     * @param PhoneNumber $phone
     * @param string $teacher
     * @param string $project
     * @param string $time
     * @return null
     */
    abstract public function sendDeadlineReminder(PhoneNumber $phone, string $teacher, string $project, string $time);


    /**
     * @param PhoneNumber $phone
     * @param string $code
     * @param array $ticket
     * @return boolean
     */
    abstract public function verify(PhoneNumber $phone, string $code, array $ticket);

    public function getIdentifier(PhoneNumber $target)
    {
        return $this->util->format($target,PhoneNumberFormat::E164);
    }
}