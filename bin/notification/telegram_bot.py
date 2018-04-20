# -*- coding: utf-8 -*-

import telegram
import config


class TelegramBot:
    nfls_bot = telegram.Bot(token=config.nfls_bot_token)
    nfls_chat_id = config.nfls_chat_id

    luogu_bot = telegram.Bot(token=config.nfls_bot_token)
    luogu_chat_id = config.nfls_chat_id

    def __init__(self):
        return

    def send_nfls(self, message):
        self.nfls_bot.send_message(chat_id=self.nfls_chat_id, text=message)

    def send_luogu(self, message):
        self.luogu_bot.send_message(chat_id=self.nfls_chat_id, text=message)
