from flask import Flask
from flask import request
from library import Library
import sys


reload(sys)
sys.setdefaultencoding('utf-8')

app = Flask(__name__)
library = Library()

@app.route("/")
def hello():
    return "Hello World!"


@app.route("/nfls/realname")
def realname():
    username = request.args.get('username')
    chinese_name = request.args.get('chinese_name')
    library.new_realname_notification(username=username, chinese_name=chinese_name)
    return "OK"
