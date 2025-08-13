from colors import *
from test_onboard import *
from test_user import *
from test_login import *
from test_country import *
from test_state import *
from test_city import*
from test_pill import*
# from test_appointment import*
from test_appointment_type import*
from test_appointment_scheduling import*


base_url = 'http://localhost:5510';
global access_token;
global user_uuid;

Colors.print_colored("\n\n ---------------------> Onboard", ColorModel.CYAN);

# Get '/'
TestOnboard.test_init(base_url);

# Get '/v1'
TestOnboard.test_init_v1(base_url);

Colors.print_colored("\n\n ---------------------> User", ColorModel.CYAN);

# POST '/v1/user'
TestUser.test_create_user(base_url);

Colors.print_colored("\n\n ---------------------> Login", ColorModel.CYAN);

# POST '/v1/login'
access_token = TestLogin.test_login(base_url);

Colors.print_colored("\n\n ---------------------> User (logged)", ColorModel.CYAN);

# GET '/v1/user'
user_uuid = TestUser.test_list_users(base_url, access_token);

# PUT '/v1/user'
TestUser.test_edit_user(base_url, access_token, user_uuid);

# DELETE '/v1/user'
TestUser.test_delete_user(base_url, access_token, user_uuid);

# PUT '/v1/user/reactivate/'
TestUser.test_reactivate_user(base_url, access_token, user_uuid);

Colors.print_colored("\n\n ---------------------> Country ", ColorModel.CYAN);
# GET '/v1/country'
TestCountry.test_list_countries(base_url, access_token);

Colors.print_colored("\n\n ---------------------> State ", ColorModel.CYAN);
# GET '/v1/state'
TestState.test_list_states(base_url, access_token);

Colors.print_colored("\n\n ---------------------> City ", ColorModel.CYAN);
 # GET '/v1/city'
TestCity.test_list_cities(base_url, access_token);

Colors.print_colored("\n\n ---------------------> Pill ", ColorModel.CYAN);
 # GET '/v1/pill'
TestPill.test_list_pills(base_url, access_token);

# # POST '/v1/pill/create_pill'
TestPill.test_create_pill(base_url, access_token);

# POST '/v1/pill/update_pill'
TestPill.test_update_pill(base_url, access_token, user_uuid);

# DELETE   '/v1/pill'
TestPill.test_delete_pill(base_url, access_token, user_uuid);

# PUT '/v1/pill/reactivate/'
TestPill.test_reactivate_pill(base_url, access_token, user_uuid);

Colors.print_colored("\n\n ---------------------> Appointment ", ColorModel.CYAN);

# POST '/v1/appointment/create_appointment'
# TestAppointment.test_create_appointment(base_url, access_token);


# #GET "/v1/appointment" 
# TestAppointment.test_list_appointments(base_url, access_token);

# # POST '/v1/appointment/update_appointment'
# TestAppointment.test_update_appointment(base_url, access_token, user_uuid);

# # DELETE   '/v1/appointment'
# TestAppointment.test_delete_appointment(base_url, access_token, user_uuid);

# # PUT '/v1/appointment/reactivate/'
# TestAppointment.test_reactivate_appointment(base_url, access_token, user_uuid);

Colors.print_colored("\n\n ---------------------> Appointment_type ", ColorModel.CYAN);

# GET '/v1/appointment_type'
TestAppointmentType.test_list_appointment_type(base_url, access_token);

# POST '/v1/appointment_type'
TestAppointmentType.test_create_appointment_type(base_url, access_token);

# DELETE '/v1/appointment_type'
TestAppointmentType.test_delete_appointment_type(base_url, access_token, user_uuid)  

# POST '/v1/appointment_type/update_appointment_type'
TestAppointmentType.test_update_appointment_type(base_url, access_token, user_uuid);

# PUT '/v1/appointment_type/reactivate'
TestAppointmentType.test_reactivate_appointment_type(base_url, access_token, user_uuid);

Colors.print_colored("\n\n ---------------------> Appointment_scheduling ", ColorModel.CYAN);

# "GET /v1/appointment/scheduling/"
TestAppointmentScheduling.test_list_appointment_scheduling(base_url, access_token);

# "POST /v1/appointment/scheduling/"'
TestAppointmentScheduling.test_create_appointment_scheduling(base_url, access_token);

# "DELETE /v1/appointment/scheduling/
TestAppointmentScheduling.test_delete_appointment_scheduling(base_url, access_token, user_uuid);

# "PUT /v1/appointment/scheduling/
TestAppointmentScheduling.test_update_appointment_scheduling(base_url, access_token, user_uuid);

# "PUT /v1/appointment/scheduling/reactivate/
TestAppointmentScheduling.test_reactivate_appointment_scheduling(base_url, access_token, user_uuid);