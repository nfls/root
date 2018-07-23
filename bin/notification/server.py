from celery import Celery
import apns2
import sendgrid
from sendgrid.helpers.mail import *
import requests
from aliyunsms import AliyunSMS
import config

app = Celery('notification', broker="redis://127.0.0.1", backend="redis://127.0.0.1")

aps = apns2.APNSClient(mode="dev", client_cert=config.apns_cert)
sg = sendgrid.SendGridAPIClient(apikey=config.sendgrid_key)

callback = config.apns_callback

@app.task(name='tasks.sendEmail')
def sendEmail(sender, sender_name, receiver, subject, content, content_type):
    mail = Mail(Email(sender, sender_name), subject, Email(receiver), Content(content_type, content))
    sg.client.mail.send.post(request_body=mail.get())

@app.task(name='tasks.sendSMS')
def sendSMS(receiver, template_code, params):
    cli = AliyunSMS(access_key_id=config.aliyun_id, access_secret=config.aliyun_key)
    cli.request(phone_numbers=receiver,
                       sign='南外人',
                       template_code=template_code,
                       template_param=params)

@app.task(name='tasks.sendAPN')
def sendAPN(token, callbackToken, title, subtitle, body, badge, custom):
    status = 0
    try:
        alert = apns2.PayloadAlert(body=body, title=title).dict()
        alert["subtitle"] = subtitle
        payload = apns2.Payload(alert=alert, badge=badge, custom=custom, mutable_content=True)
        n = apns2.Notification(payload=payload, priority=apns2.PRIORITY_HIGH)
        result = aps.push(n=n, device_token=token)
        if result.status_code == 200:
            status = 1
        if result.reason == "BadDeviceToken" or result.reason == "Unregistered":
            status = -1
    except:
        status = 0

    requests.post(callback, data={
        "callbackToken": callbackToken,
        "type": "server",
        "status": status
    })
