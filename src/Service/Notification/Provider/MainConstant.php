<?php

namespace App\Service\Notification\Provider;

class MainConstant
{
    function renderCodePage($actionCN, $actionEN, $code, $language = "zh-cn")
    {
        $text =
            <<<EOD
<div style="position:relative;margin:0px auto;width:600px;overflow:hidden">
    <div><img src="https://nfls-public.oss-cn-shanghai.aliyuncs.com/uploads/64FhtpS2nn7dSNwMNkBtrGcsWn2dfihm.png" width="50">
        <div style="display:inline-block;float:right;color:inherit;text-decoration:inherit;">NFLSIO / 南外人 <br><a href="https://nfls.io/">https://nfls.io </a>
        </div>
    </div>
    <hr>
    <div style="position:relative;z-index:2">
        <h1 style="color:inherit;text-decoration:inherit;">NFLSIO / 南外人 </h1>
        <p>您好！您在进行$actionCN 的操作。其验证码是</p>
        <code>$code </code>
        <p>请将其完整复制粘贴至对应的输入栏内。如果您没有进行相关操作，请无视本邮件</p>
        <p>&nbsp;</p>
        <hr>
        <p>Hello! Your verification code for $actionEN is</p>
        <code>$code</code>
        <p>Please completely copy and paste it into the correct field. If you did not perform such action, please ignore this email.</p>
        <p>&nbsp;</p>
        <hr>
        <small>这篇邮件是由系统自动发送，请不要回复这封邮件。 <br>This email is sent by the system automatically. Please do not reply it.</small>
    </div>
</div>
EOD;
        return $text;
    }
}