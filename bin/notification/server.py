from celery import Celery
import apns2
import requests

app = Celery('notification', broker="redis://127.0.0.1", backend="redis://127.0.0.1")

aps = apns2.APNSClient(mode="dev", client_cert="/etc/cert/push_dev.pem")
callback = "https://nfls.io/device/pushCallback"


@app.task
def add(x, y):
    return x+y


@app.task
def delete(x, y):
    return x-y


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
