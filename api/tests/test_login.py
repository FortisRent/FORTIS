from colors import *
import requests
import json

class TestLogin:
    def test_login(base_url):
        response = requests.post(url = (base_url + '/v1/login'), auth=('joao@g1112l.com', '123456'))

        if response.status_code != 200:
            print(response.status_code);
            Colors.print_colored("Erro no request - 'POST - /v1/login' - " + res, ColorModel.RED);
            
        res = json.loads(response.text)

        if res['access_token'] or res['user']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/login", ColorModel.GREEN);
            return res['access_token']
        else:
            Colors.print_colored("Erro no request - 'POST - /v1/login' - " + res, ColorModel.RED);
            