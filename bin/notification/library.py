# -*- coding: utf-8 -*-

import telegram_bot


class Library:
    bot = telegram_bot.TelegramBot()

    def __init__(self):
        return

    def new_realname_notification(self, username, chinese_name):
        message = "新的实名认证请求，来自 " + chinese_name + " (" + username + ")"
        self.bot.send_nfls(message=message)

    def gogs_webhook(self, playload):
        return
