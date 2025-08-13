from colors import *
import requests
import json

class TestState:



    # GET '/v1/state'
    def test_list_states(base_url, access_token):
        response = requests.get(url=base_url + '/v1/state', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/state'", ColorModel.RED)

        res = json.loads(response.text)
 
        if res['states']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/state", ColorModel.GREEN)
            return res['states']
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/state' - " + res, ColorModel.RED)


