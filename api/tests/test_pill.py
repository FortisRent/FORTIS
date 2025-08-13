from colors import *
import requests
import json

class TestPill:



    # GET '/v1/pill'
    def test_list_pills(base_url, access_token):
       
        response = requests.get(url=base_url + '/v1/pill', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/pill'", ColorModel.RED)

        res = json.loads(response.text)
        if res['pills']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/pill", ColorModel.GREEN)
            return res['pills'][0]['uuid']
        
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/pill' - " + str(res), ColorModel.RED)





    # POST '/v1/pill/'
    def test_create_pill(base_url, access_token):
        
        new_pill = {
            "name": "Ritalina",
            "quantity": 15,
            "interval": 8,
            "days": 5
        }

        data_obj = json.dumps(new_pill)

        response = requests.post(url = (base_url + '/v1/pill'), data = data_obj, headers={'token': access_token})

        if response.status_code != 201 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'POST - /v1/pill'", ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/pill", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'POST - /v1/pill'", ColorModel.RED)





    # POST '/v1/pill/update_pill'
    def test_update_pill(base_url, access_token, user_uuid):
            
        new_pill = {
            "name": "Ritalina",
            "quantity": 16,
            "interval": 8,
            "days": 7
        }

        data_obj = json.dumps(new_pill)

        response = requests.put(url = (base_url + '/v1/pill/' + user_uuid), data = data_obj , headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/pill/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/pill/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/pill/'", ColorModel.RED)






     # DELETE   '/v1/pill'
    def test_delete_pill(base_url, access_token, user_uuid):
            
        response = requests.delete(url = (base_url + '/v1/pill/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'DELETE - /v1/pill/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: DELETE - /v1/pill/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'DELETE - /v1/pill/'", ColorModel.RED)






    # PUT '/v1/pill/reactivate/'
    def test_reactivate_pill(base_url, access_token, user_uuid):
            
        response = requests.put(url = (base_url + '/v1/pill/reactivate/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/pill/reactivate/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/pill/reactivate/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/pill/reactivate/'", ColorModel.RED)