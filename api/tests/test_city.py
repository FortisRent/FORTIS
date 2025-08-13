from colors import *
import requests
import json

class TestCity:



    # GET '/v1/city'
    def test_list_cities(base_url, access_token):
        response = requests.get(url=base_url + '/v1/city', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/city'", ColorModel.RED)

        res = json.loads(response.text)
        if res['cities']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/city", ColorModel.GREEN)
            return res['cities'][0]['state_id']
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/city' - " + res, ColorModel.RED)
