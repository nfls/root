<?php

namespace App\Service\Notification\Provider;

use Parsedown;

class MainConstant
{
    function renderCodePage($actionCN, $actionEN, $code)
    {

        $text = <<<EOD
        <p>您好！您在进行$actionCN 的操作。其验证码是</p>
        <code>$code</code>
        <p>请将其完整复制粘贴至对应的输入栏内。如果您没有进行相关操作，请无视本邮件</p>
        <p>&nbsp;</p>
        <hr>
        <p>Hello! Your verification code for $actionEN is</p>
        <code>$code</code>
        <p>Please completely copy and paste it into the correct field. If you did not perform such action, please ignore this email.</p>
        <p>&nbsp;</p>
EOD;
        return $this->base($text);
    }

    function renderRealnameFailed()
    {
        $text = <<<EOD
        <p>用户您好，您的实名认证申请已被退回，请登录查看详情。</p>
        <p>Hello, your request to become a verified use has been rejected. Login to see more details</p> 
        <p>&nbsp;</p>
EOD;
        return $this->base($text);
    }

    function renderRealnameSucceeded(string $statusCN,string $statusEN, string $time)
    {
        $text = <<<EOD
        <p>用户您好，您的实名认证已通过审核，当前身份为$statusCN ，有效期至$time 。</p>
        <p>Hello, now you are verified. Your identity is $statusEN with expire date on $time .</p> 
        <p>&nbsp;</p>
EOD;
        return $this->base($text);
    }

    function renderNoticePage(string $teacher, string $class, string $notice)
    {
        $paser = new Parsedown();
        $notice = $paser->parse($notice);
        $text = <<<EOD
        <p><strong>$teacher</strong> posted a new note on the blackboard for group <strong>$class</strong>:</p>
        $notice
        <p>&nbsp;</p>
EOD;
        return $this->base($text);
    }

    function renderDeadlinePage(string $teacher, string $project, string $time, string $notice)
    {
        $paser = new Parsedown();
        $notice = $paser->parse($notice);
        $text = <<<EOD
        <p><strong>$teacher</strong> wants to remind you that the deadline for <strong>$project</strong> is <strong>$time</strong>.</p>
        $notice
        <p>Please schedule your time wisely, and we hope that you can finish all the work in time.</p>
        <p>&nbsp;</p>
EOD;
        return $this->base($text);
    }

    function renderNewMessagePage(string $sender, string $content)
    {
        $paser = new Parsedown();
        $content = $paser->parse($content);
        $text = <<<EOD
        <p>您收到了来自 <strong>$sender</strong>的一封新私信。</p>
        <p>You have received a PM from <strong>$sender</strong>.</p>
        $content
        <p>&nbsp;</p>
EOD;
        return $this->base($text);
    }

    function base($html)
    {
        return <<<EOD
<div style="position:relative;margin:0px auto;width:600px;overflow:hidden">
    <div><img src="https://nfls-public.oss-cn-shanghai.aliyuncs.com/uploads/64FhtpS2nn7dSNwMNkBtrGcsWn2dfihm.png" width="50">
        <div style="display:inline-block;float:right;color:inherit;text-decoration:inherit;">NFLSIO / 南外人 <br><a href="https://nfls.io/">https://nfls.io </a>
        </div>
    </div>
    <hr>
    <div style="position:relative;z-index:2">
        <h1 style="color:inherit;text-decoration:inherit;">NFLSIO / 南外人 </h1>
        $html
        <hr>
        <small>这篇邮件是由系统自动发送，请不要回复这封邮件。 <br>This email is sent by the system automatically. Please do not reply it.</small>
    </div>
</div>
EOD;
    }
}