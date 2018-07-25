<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/22
 * Time: 5:51 PM
 */

namespace App\Service;


use App\Entity\School\Alumni;
use App\Entity\School\Notice;
use App\Entity\User\Chat;
use App\Entity\User\Device;
use App\Entity\User\User;
use App\Model\MailConstant;
use App\Type\AliyunTemplateType;
use App\Type\CodeActionType;
use App\Type\DeviceType;
use Doctrine\Common\Persistence\ObjectManager;

class NotificationService
{
    /**
     * @var SMSService
     */
    private $SMSService;
    /**
     * @var MailService
     */
    private $mailService;
    /**
     * @var APNSService
     */
    private $APNService;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var MailConstant
     */
    private $mailRenderer;
    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct(ObjectManager $objectManager, CacheService $cacheService, SMSService $SMSService, MailService $mailService, APNSService $APNSService)
    {
        $this->objectManager = $objectManager;
        $this->SMSService = $SMSService;
        $this->mailService = $mailService;
        $this->APNService = $APNSService;
        $this->cacheService = $cacheService;
        $this->mailRenderer = new MailConstant();
    }

    public function code($email = null, $phone = null, int $action) {
        switch ($action) {
            case CodeActionType::REGISTER:
                if (!is_null($email) && !$this->isEmailUsed($email)) {
                    $code = $this->getRandomString();
                    $this->cacheService->codeWrite($email, $code, $action);
                    return $this->mailService->send(
                        "no-reply@nfls.io",
                        "NFLS.IO/南外人",
                        $email,
                        "【NFLS.IO/南外人】账户注册 Account Registering",
                        $this->mailRenderer->renderCodePage(
                            "注册新账户",
                            "registering a new account",
                            $code
                        ));
                } else if (!is_null($phone) && !$this->isPhoneUsed($phone)) {
                    $code = $this->getRandomCode();
                    $this->cacheService->codeWrite($phone, $code, $action);
                    return $this->SMSService->send(
                        $phone,
                        AliyunTemplateType::REGISTER,
                        ["code" => $code]);

                }
                return false;
            case CodeActionType::RESET:
                if (!is_null($email) && $this->isEmailUsed($email)) {
                    $code = $this->getRandomString();
                    $this->cacheService->codeWrite($email, $code, $action);
                    return $this->mailService->send(
                        "no-reply@nfls.io",
                        "NFLS.IO/南外人",
                        $email,
                        "【NFLS.IO/南外人】重置密码 Password Resetting",
                        $this->mailRenderer->renderCodePage(
                            "重置账户密码",
                            "resetting your password",
                            $code
                        ));
                } else if (!is_null($phone) && $this->isPhoneUsed($phone)) {
                    $code = $this->getRandomCode();
                    $this->cacheService->codeWrite($phone, $code, $action);
                    return $this->SMSService->send(
                        $phone,
                        AliyunTemplateType::RESET,
                        ["code" => $code]);
                }
                return false;
            case CodeActionType::BIND:
                if (!is_null($email) && !$this->isEmailUsed($email)) {
                    $code = $this->getRandomString();
                    $this->cacheService->codeWrite($email, $code, $action);
                    return $this->mailService->send(
                        "no-reply@nfls.io",
                        "NFLS.IO/南外人",
                        $email,
                        "【NFLS.IO/南外人】邮箱绑定 Email Binding",
                        $this->mailRenderer->renderCodePage(
                            "绑定当前邮箱",
                            "binding this email with your account",
                            $code
                        ));
                } else if (!is_null($phone) && !$this->isPhoneUsed($phone)) {
                    $code = $this->getRandomCode();
                    $this->cacheService->codeWrite($phone, $code, $action);
                    return $this->SMSService->send(
                        $phone,
                        AliyunTemplateType::BIND,
                        ["code" => $code]);

                }
                return false;
        }
        return false;
    }

    /**
     * 返回值false时为邮箱，true时为手机，null时为不正确。
     */
    public function verify($email = null, $phone = null, string $code, int $action) {
        if(!is_null($email)) {
            if($this->cacheService->codeVerify($email, $code, $action))
                return false;
            else
                return null;
        } else if(!is_null($phone)) {
            if($this->cacheService->codeVerify($phone, $code, $action))
                return true;
            else
                return null;
        }
        return null;
    }

    public function realnamePassed(User $user, Alumni $alumni) {
        $expiryCN = "无期限";
        $expiryEN = "unlimited";
        $statusCN = $alumni->readableUserStatus()["zh"];
        $statusEN = $alumni->readableUserStatus()["en"];
        if(!is_null($alumni->getExpireAt())) {
            $expiryCN = $alumni->getExpireAt()->format("Y-m-d");
            $expiryEN = $expiryCN;
        }
        $this->mailService->send(
            "alumni@nfls.io",
            "南外人实名认证系统",
            $user->getEmail(),
            "【NFLS.IO/南外人】实名认证 Verification",
            $this->mailRenderer->renderRealnameSucceeded(
                $statusCN,
                $statusEN,
                $expiryCN,
                $expiryEN));
        $this->APNService->bulk(
            $this->getDevices($user),
            "实名认证审核通过",
            "Verification passed",
            "您的实名认证审核已通过，有效期至$expiryCN ，当前身份为$statusCN 。You have been verified as $statusEN, which expires on $expiryEN",
            null,
            null,
            null);
        $this->SMSService->send(
            $user->getPhone(),
            AliyunTemplateType::REALNAME_SUCCEEDED,
            array("time"=>$expiryCN, "status"=>$statusCN));
    }

    public function realnameFailed(User $user) {
        $this->mailService->send(
            "alumni@nfls.io",
            "南外人实名认证系统",
            $user->getEmail(),
            "【NFLS.IO/南外人】实名认证 Verification",
            $this->mailRenderer->renderRealnameFailed());
        $this->APNService->bulk(
            $this->getDevices($user),
            "实名认证审核退回",
            "Verification failed",
            "您的实名认证申请已被退回。Your verification request has been rejected.",
            null,
            null,
            null);
        $this->SMSService->send(
            $user->getPhone(),
            AliyunTemplateType::REALNAME_FAILED,
            array());
    }

    public function notifyNewMessage(Chat $chat) {
        $this->mailService->send(
            "message@nfls.io",
            "南外人私信",
            $chat->getReceiver()->getEmail(),
            "【NFLS.IO/南外人】私信 PM",
            $this->mailRenderer->renderNewMessagePage($chat->getSender()->getUsername(), $chat->getContent())
        );
        $this->APNService->bulk(
            $this->getDevices($chat->getReceiver()),
            "来自 ".$chat->getSender()->getUsername()." 的新私信",
            "New message from ".$chat->getSender()->getUsername(),
            $chat->getContent(),
            null,
            null,
            null);
        $this->SMSService->send($chat->getReceiver()->getPhone(),
            AliyunTemplateType::MESSAGE,
            array());
    }

    private function getDevices(User $user) {
        return $this->objectManager->getRepository(Device::class)->findByUserAndType($user, DeviceType::IOS);
    }

    private function isPhoneUsed(?string $phone) {
        return ($this->objectManager->getRepository(User::class)->findOneBy(["phone" => $phone]) instanceof User);
    }

    private function isEmailUsed(?string $phone) {
        return ($this->objectManager->getRepository(User::class)->findOneBy(["email" => $phone]) instanceof User);
    }

    private function getRandomString() {
        return base64_encode(random_bytes(16));
    }

    private function getRandomCode() {
        return (string)mt_rand(100000, 999999);
    }
}