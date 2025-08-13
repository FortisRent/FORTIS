from colors import *
import requests
import json

class TestAppointmentType:


    # POST '/v1/appointment_type/create_appointment_type'
    def test_create_appointment_type(base_url, access_token):
        
        new_appointment_type = {
            "name": "consultorio testelalalalla",
            "description":"testando2323"
	 }

        data_obj = json.dumps(new_appointment_type)
        
        response = requests.post(url = (base_url + '/v1/appointment_type'), data = data_obj, headers={'token': access_token})

        if response.status_code != 201 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'POST - /v1/appointment_type'", ColorModel.RED)
      

        res = json.loads(response.text)
        print(res)
       

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/appointment_type", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'POST - /v1/appointment_type'", ColorModel.RED)





    # GET '/v1/appointment_type'

    def test_list_appointment_type(base_url, access_token):
        response = requests.get(url=base_url + '/v1/appointment_type', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/appointment_type'", ColorModel.RED)

        res = json.loads(response.text)
       
        if res['appointment_type']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/appointment_type", ColorModel.GREEN)
            return res['appointment_type']
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/appointment_type' - " + res, ColorModel.RED)




    
     # DELETE  '/v1/appointment_type'

    def test_delete_appointment_type(base_url, access_token, user_uuid):
            
        response = requests.delete(url = (base_url + '/v1/appointment_type/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'DELETE - /v1/appointment_type/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: DELETE - /v1/appointment_type/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'DELETE - /v1/appointment_type/'", ColorModel.RED)





        # POST '/v1/appointment_type/update_appointment_type'
    def test_update_appointment_type(base_url, access_token, user_uuid):
            
        new_appointment_type = {
            "name": "consultorio de cachoirro ",
            "description":"clinica aauau"
        }

        data_obj = json.dumps(new_appointment_type)

        response = requests.put(url = (base_url + '/v1/appointment_type/' + user_uuid), data = data_obj , headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment_type/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/appointment_type/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment_type/'", ColorModel.RED)




    # PUT '/v1/appointment_type/reactivate/'
    def test_reactivate_appointment_type(base_url, access_token, user_uuid):
            
        response = requests.put(url = (base_url + '/v1/appointment_type/reactivate/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment_type/reactivate/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/appointment_type/reactivate/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment_type/reactivate/'", ColorModel.RED)