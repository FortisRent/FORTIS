from colors import *
import requests
import json

class TestUser:

    # POST '/v1/user'
    def test_create_user(base_url):
        new_user = {
            "name": "João",
            "cpf": "123.111.789-33",
            "email": "joao@g1112l.com",
            "phone": "(48) 9 9831-1228",
            "password":"123456",
            "birthdate": "2000-01-20 00:00:00",
            "is_admin": False,
            "rg":"5.560.966",
            "emergency_number":"(48) 9 9831-1228",
            "emergency_name":"clotilde"
        }

        data_obj = json.dumps(new_user)

        response = requests.post(url = (base_url + '/v1/user'), data = data_obj)

        if response.status_code != 201 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'POST - /v1/user'", ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message'] or res['user']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/user", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'POST - /v1/user'", ColorModel.RED)


    # GET '/v1/user'
    def test_list_users(base_url, access_token):
        response = requests.get(url=base_url + '/v1/user', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/user'", ColorModel.RED)

        res = json.loads(response.text)

        if res['users'] or res['message']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/user", ColorModel.GREEN)
            return res['users'][-1]['uuid']
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/user' - " + res, ColorModel.RED)

    
    def test_edit_user(base_url, access_token, user_uuid):
            
        new_user = {
            "name": "João",
            "cpf": "123.111.789-33",
            "email": "codorna@g1112l.com",
            "phone": "(48) 9 9831-1228",
            "password":"123456",
            "birthdate": "2000-01-20 00:00:00",
            "is_admin": False,
            "rg":"5.560.966",
            "emergency_number":"(48) 9 9831-1228",
            "emergency_name":"clotilde"
        }

        data_obj = json.dumps(new_user)

        response = requests.put(url = (base_url + '/v1/user/' + user_uuid), data = data_obj , headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/user/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message'] or res['user']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/user/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/user/'", ColorModel.RED)


    #DELETE   '/v1/user'
    def test_delete_user(base_url, access_token, user_uuid):
            
        response = requests.delete(url = (base_url + '/v1/user/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'DELETE - /v1/user/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message'] or res['user']:
            Colors.print_colored("\n Endpoint funcionando: DELETE - /v1/user/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'DELETE - /v1/user/'", ColorModel.RED)

    #PUT  '/v1/user'
    def test_reactivate_user(base_url, access_token, user_uuid):
            
        response = requests.put(url = (base_url + '/v1/user/reactivate/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/user/reactivate/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message'] or res['user']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/user/reactivate/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/user/reactivate/'", ColorModel.RED)