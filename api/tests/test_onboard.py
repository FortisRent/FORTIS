from colors import *
import requests
import json

class TestOnboard:
    def test_init(base_url):
        response = requests.get(base_url + '/');

        if response.status_code != 200:
            print("Erro no request - '/'", ColorModel.RED);

        res = json.loads(response.text);

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: /", ColorModel.GREEN);
        else:
            Colors.print_colored("Erro no request - '/'", ColorModel.RED);


    def test_init_v1(base_url):
        response = requests.get(base_url + '/v1')

        # Test status.
        if response.status_code != 200:
            Colors.print_colored("Erro no request - '/v1/'", ColorModel.RED)

        res = json.loads(response.text)

        # Test message.
        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: /v1/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - '/v1/'", ColorModel.RED)