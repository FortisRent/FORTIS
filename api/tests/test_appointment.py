from colors import *
import requests
import json

class TestAppointment:


    # # POST '/v1/appointment/create_appointment'
    def test_create_appointment(base_url, access_token):
        
        new_appointment = {
                "amount":"150",
                "appointment_type_id":"1",
                "lenght":"60",
                "description":"Consulta especializada da nossa clinica",
                "clinic_id":"4"
	 }

        data_obj = json.dumps(new_appointment)
        
        response = requests.post(url = (base_url + '/v1/appointment'), data = data_obj, headers={'token': access_token})

        if response.status_code != 201 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'POST - /v1/appointment'", ColorModel.RED)
      

        res = json.loads(response.text)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/appointment", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'POST - /v1/appointment'", ColorModel.RED)





    # GET '/v1/appointment'
    def test_list_appointments(base_url, access_token):
       
        response = requests.get(url=base_url + '/v1/appointment', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/appointment'", ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['appointments'] or ['appointment']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/appointment", ColorModel.GREEN)
            return res['appointments']
        
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/appointment' - " + str(res), ColorModel.RED)







    # POST '/v1/appointment/update_appointment'
    def test_update_appointment(base_url, access_token, user_uuid):
            
        new_appointment = {
	
		"amount": "150",
		"appointment_type_id": 1,
	 	"lenght": 60,
	 	"description": "Consulta especializada da nossa shopping",
         "clinic_id": 4
	 }

        data_obj = json.dumps(new_appointment)

        response = requests.put(url = (base_url + '/v1/appointment/' + user_uuid), data = data_obj , headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/appointment/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/'", ColorModel.RED)






     # DELETE   '/v1/appointment'
    def test_delete_appointment(base_url, access_token, user_uuid):
            
        response = requests.delete(url = (base_url + '/v1/appointment/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'DELETE - /v1/appointment/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: DELETE - /v1/appointment/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'DELETE - /v1/appointment/'", ColorModel.RED)






    # PUT '/v1/appointment/reactivate/'
    def test_reactivate_appointment(base_url, access_token, user_uuid):
            
        response = requests.put(url = (base_url + '/v1/appointment/reactivate/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/reactivate/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/appointment/reactivate/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/reactivate/'", ColorModel.RED)