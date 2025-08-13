from colors import *
import requests
import json

class TestAppointmentScheduling:


    # "POST /v1/appointment/scheduling/"'
    def test_create_appointment_scheduling(base_url, access_token):
        
        new_appointment_scheduling = {
            "user_id": 10,
	 	    "clinic_id": 4,
		    "doctor_id": 1,
	        "employee_id": 9,
		    "due_date": "2024/10/23",
		    "description": "Consulta com especialista sensacional e sem obs.",
	 	    "discount_amount": 10
	 }

        data_obj = json.dumps(new_appointment_scheduling)
        
        response = requests.post(url = (base_url + '/v1/appointment/scheduling'), data = data_obj, headers={'token': access_token})

        if response.status_code != 201 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'POST - /v1/appointment/scheduling'", ColorModel.RED)
      

        res = json.loads(response.text)
        print(res)
       

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: POST - /v1/appointment/scheduling", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'POST - /v1/appointment/scheduling'", ColorModel.RED)





    # "GET /v1/appointment/scheduling/"

    def test_list_appointment_scheduling(base_url, access_token):
        response = requests.get(url=base_url + '/v1/appointment/scheduling', headers={'token': access_token})

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.RED)
            Colors.print_colored("Erro no request - 'GET - /v1/appointment/scheduling'", ColorModel.RED)

        res = json.loads(response.text)
        print(res)
       
        if res['appointment_scheduling']:
            Colors.print_colored("\n Endpoint funcionando: GET - /v1/appointment/scheduling", ColorModel.GREEN)
            return res['appointment_scheduling']
        else:
            Colors.print_colored("Erro no request - 'GET - /v1/appointment/scheduling' - " + res, ColorModel.RED)




    
    # "DELETE /v1/appointment/scheduling/

    def test_delete_appointment_scheduling(base_url, access_token, user_uuid):
            
        response = requests.delete(url = (base_url + '/v1/appointment/scheduling/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'DELETE - /v1/appointment/scheduling/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: DELETE - /v1/appointment/scheduling/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'DELETE - /v1/appointment/scheduling/'", ColorModel.RED)





    # "PUT /v1/appointment/scheduling/
    def test_update_appointment_scheduling(base_url, access_token, user_uuid):
            
        new_appointment_scheduling = {
            "user_id": 10,
	 	    "clinic_id": 4,
		    "doctor_id": 1,
	        "employee_id": 9,
		    "due_date": "2024/10/23",
		    "description": "Consulta com especialista sensacional e sem obs.",
	 	    "discount_amount": 10                
        }

        data_obj = json.dumps(new_appointment_scheduling)

        response = requests.put(url = (base_url + '/v1/appointment/scheduling/' + user_uuid), data = data_obj , headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/scheduling/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/appointment/scheduling/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/scheduling/'", ColorModel.RED)




    # "PUT /v1/appointment/scheduling/reactivate/
    def test_reactivate_appointment_scheduling(base_url, access_token, user_uuid):
            
        response = requests.put(url = (base_url + '/v1/appointment/scheduling/reactivate/' + user_uuid), headers={ 'token': access_token })

        if response.status_code != 200 and response.status_code != 502:
            Colors.print_colored(str(response.status_code), ColorModel.YELLOW)
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/scheduling/reactivate/'" + user_uuid, ColorModel.RED)

        res = json.loads(response.text)
        print(res)

        if res['message']:
            Colors.print_colored("\n Endpoint funcionando: PUT - /v1/appointment/scheduling/reactivate/", ColorModel.GREEN)
        else:
            Colors.print_colored("Erro no request - 'PUT - /v1/appointment/scheduling/reactivate/'", ColorModel.RED)