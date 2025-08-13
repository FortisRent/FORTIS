from colors import *
import requests
import json

class TestCountry:



    # GET '/v1/country'
    def test_list_countries(base_url, access_token):
        response = requests.get(url=base_url + '/v1/country', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/country'", ColorModel.RED)

        res = json.loads(response.text)
 
 
        if res['countries']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/country", ColorModel.GREEN)
            return res['countries']
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/country' - " + res, ColorModel.RED)


