<?php
	$routes = [

		// Init
		"GET /" 								                    => ["OnboardController", "init"],				// TEST - Init
		"GET /v1" 								                    => ["OnboardController", "init_v1"],			// TEST - API Init

		// Login
		"POST /v1/login" 						                    => ["LoginController", "login"],				// TEST - Login
		"POST /v1/token/validate" 				                    => ["LoginController", "validate_token"],		// TEST - Validate token

		// Resetar senha
		"POST /v1/password/email" 				                    => ["PasswordController", "validate_email"],	// TEST - Qual o seu email? -> Se email correto, envia código.
		"POST /v1/password/code"				                    => ["PasswordController", "validate_code"], 	// TEST - Qual o código? -> Se código correto, segue para a próxima tela.
		"PUT /v1/password/update" 				                    => ["PasswordController", "update_password"], 	// TEST - Adicionar nova senha + confirmação.

		// Usuários
		"GET /v1/user/" 							                    => ["UserController", "get_users"],			        // TEST - Get all users
		"GET /v1/user/logged/" 					                    => ["UserController", "get_user_by_uuid_logged"], 	        // TEST - Get details from an user logged.
		"GET /v1/user/([\w-]+)" 					                    => ["UserController", "get_user_by_uuid"], 	        // TEST - Get details from an user.
		"GET /v1/user/logged/profile/" 					                    => ["UserController", "get_profiles"], 	        // TEST - Get details from an user.
		"POST /v1/user/" 						                    => ["UserController", "create_user"], 		        // TEST - Insert a new user.
		"POST /v1/user/profile/upload/" 	                   	 	=> ["UserController", "upload_profile_picture"], 	// TEST - Upload profile image.
		"POST /v1/user/background/upload/" 	                		=> ["UserController", "upload_background_picture"], // TEST - Upload background image.
		"PUT /v1/user/logged/" 					                    => ["UserController", "update_user_logged"], 		        // TEST - Update user.
		"PUT /v1/user/([\w-]+)" 					                    => ["UserController", "update_user"], 		        // TEST - Update user.
		"DELETE /v1/user/([\w-]+)"					                    => ["UserController", "delete_user"], 		        // TEST - Delete user.
		"PUT /v1/user/reactivate/([\w-]+)" 		                    => ["UserController", "reactivate_user"], 	        // TEST - Reactivate user.

		// FAZER - Endereços de um usuário
		"GET /v1/user/address/" 								    => ["UserAddressController", "get_user_address"],		            // TEST - Get all address
		"GET /v1/user/address/([\w-]+)" 	         			  	    => ["UserAddressController", "get_user_address_by_address_uuid"], 	// TEST - Get details from address.
		"GET /v1/user/address/logged/" 								=> ["UserAddressController", "get_user_address_by_logged"],      // TEST - Get address from user.
		"GET /v1/user/address/user/([\w-]+)"							=> ["UserAddressController", "get_user_address_by_user_uuid"], 	    // TEST - Get address from user.
		"POST /v1/user/address/" 									=> ["UserAddressController", "create_user_address"],	            // TEST - Insert a new address
		"PUT /v1/user/address/([\w-]+)" 								=> ["UserAddressController", "update_user_address"],		        // TEST - Update address.
		"DELETE /v1/user/address/([\w-]+)"								=> ["UserAddressController", "delete_user_address"],		        // TEST - Delete address.
		"PUT /v1/user/address/reactivate/([\w-]+)" 					=> ["UserAddressController", "reactivate_user_address"], 	       	// TEST - Reactivate address.

		// Países
		"GET /v1/country" 						                    	=> ["CountryController", "get_countries"],		// TEST - Get all countries

		// Estados
		"GET /v1/state" 						                    		=> ["StateController", "get_state"],		    // TEST - Get all states

		// Cidades
		"GET /v1/city" 							                    		=> ["CityController", "get_city"],				// TEST - Get all cities
		"GET /v1/city/state/([\w-]+)" 				                    => ["CityController", "get_city_by_geo_state"],	// TEST - Get all cities by state id


		// //	Log staging.
		// "GET /v1/log/stage/" 									=> ["LogStageController", "get_log_stage"],			        // TEST - Get all log_stages
		// "GET /v1/log/stage/([\w-]+)" 								=> ["LogStageController", "get_log_stage_by_uuid"],			// TEST - Get uuid log_stages
		// "POST /v1/log/stage/" 								    => ["LogStageController", "create_log_stage"], 		        // TEST - Insert a log.
		// "PUT /v1/log/stage/([\w-]+)" 							    => ["LogStageController", "update_log_stage"], 		        // TEST - Update log.
		// "DELETE /v1/log/stage/([\w-]+)"						 	=> ["LogStageController", "delete_log_stage"], 		  		// TEST - Delete log.
		// "PUT /v1/log/stage/reactivate/([\w-]+)" 				    => ["LogStageController", "reactivate_log_stage"], 	       	// TEST - Reactivate drugstore.

		// Meios de pagamentos
		"GET /v1/payment/method/" 				                    => ["PaymentMethodController", "get_payment_methods"],	// TEST - Get all payment methods


		// Review
		// "GET /v1/review" 									        => ["ReviewController", "get_review"],			                // TEST - Lista todas as avaliações
		// "GET /v1/review/([\w-]+)" 							            => ["ReviewController", "get_review_by_uuid"], 	                // TEST - Retorna os detalhes de uma avaliação.
		// "GET /v1/review/doctor/([\w-]+)" 							    => ["ReviewController", "get_review_by_doctor_uuid"], 	        // TEST - Lista as avaliações de uma clínica.
		// "GET /v1/review/doctor/average/([\w-]+)" 					    => ["ReviewController", "get_doctor_average_score"], 	        // TEST - Nota média das avaliações de uma clínica.
		// "GET /v1/review/logged/" 							        => ["ReviewController", "get_review_by_logged_user"], 	        // TEST - Lista as avaliações feitas por um usuário.
		// "POST /v1/review" 								            => ["ReviewController", "create_review"], 		                // TEST - Cria uma nova avaliação.
		// "PUT /v1/review/([\w-]+)" 								        => ["ReviewController", "edit_review"], 		                // TEST - Edita uma avaliação.
		// "DELETE /v1/review/([\w-]+)"							        => ["ReviewController", "delete_review"], 		                // TEST - Deleta uma avaliação.
		// "PUT /v1/review/reactivate/([\w-]+)" 						    => ["ReviewController", "reactivate_review"], 		            // TEST - Reativa uma avaliação.

		// Cargos
		"GET /v1/role/" 									        => ["RoleController", "get_role"],			        // TEST - Get all roles
		"GET /v1/role/([\w-]+)" 							            => ["RoleController", "get_role_by_uuid"], 	        // TEST - Get details from an role.
		"GET /v1/role/company/([\w-]+)" 							            => ["RoleController", "get_role_by_company_uuid"], 	        // TEST - Get roles by company_uuid.
		"POST /v1/role/" 								            => ["RoleController", "create_role"], 		        // TEST - Insert a new role.
		"PUT /v1/role/([\w-]+)" 							            => ["RoleController", "update_role"], 		        // TEST - Update role.
		"DELETE /v1/role/([\w-]+)"							            => ["RoleController", "delete_role"], 		        // TEST - Delete role.
		"PUT /v1/role/reactivate/([\w-]+)" 				            => ["RoleController", "reactivate_role"], 	       	// TEST - Reactivate role.

		// // Documentos necessários para cada cargo.
		// "GET /v1/role/document/" 								=> ["RoleDocumentController", "get_role_document"],			// TEST - Get all roles documents
		// "GET /v1/role/document/([\w-]+)" 							=> ["RoleDocumentController", "get_role_document_by_uuid"], // TEST - Get details from an role/document.
		// "POST /v1/role/document/" 								=> ["RoleDocumentController", "create_role_document"], 		// TEST - Insert a new role/document.
		// "PUT /v1/role/document/([\w-]+)" 							=> ["RoleDocumentController", "update_role_document"], 		// TEST - Update role/document.
		// "DELETE /v1/role/document/([\w-]+)"						=> ["RoleDocumentController", "delete_role_document"], 		// TEST - Delete role/document.
		// "PUT /v1/role/document/reactivate/([\w-]+)" 				=> ["RoleDocumentController", "reactivate_role_document"], 	// TEST - Reactivate role document.


		// Payment - Pagamentos
		"GET /v1/payment/doctor/" 								            => ["PaymentController", "get_payment_summary_by_doctor_uuid"],					// TEST - Data of the dashboard from the admin page.
		"GET /v1/financial/doctor/" 								            => ["PaymentController", "get_today_payment"],													// OK - Get all the verified payments of the day from a doctor.
		"GET /v1/financial/doctor/month/" 								            => ["PaymentController", "get_payment_thirty_days"],								// OK - Get all the verified payments of 30 days from a doctor.
		"GET /v1/financial/doctor/biweekly/" 								            => ["PaymentController", "get_payment_biweekly_days"],						// OK - Get all the verified payments of 15 days from a doctor.
		"GET /v1/financial/doctor/weekly/" 								            => ["PaymentController", "get_payment_weekly_days"],							// OK - Get all the verified payments of 7 days from a doctor.

		"GET /v1/payment/doctor/financial/" 								            => ["PaymentController", "get_payment_financial_by_doctor_uuid"], // OK - Get all the data for the financial page.

		// Financeiro - OMIE
		"GET /v1/omie/client/list/"                  => ["OmieController", "get_clients"],
		"POST /v1/omie/client/new/"                  => ["OmieController", "create_client"],
		"POST /v1/omie/cobranca/new/"                  => ["OmieController", "create_cobranca"],
		"POST /v1/omie/boleto/new/"                  => ["OmieController", "create_boleto"],

		// Empresas
		"GET /v1/company/" 								            => ["CompanyController", "get_all_companies"],			        // TEST - Get all companys
		"GET /v1/company/([\w-]+)" 							            => ["CompanyController", "get_company_by_uuid"], 	        // TEST - Get details from an company.
		"GET /v1/company/logged/" 									=> ["CompanyController", "get_company_by_logged"], 	// TEST - Get companys from user.
		"GET /v1/company/address/([\w-]+)" 									=> ["CompanyController", "get_all_company_address"], 	// TEST - Get companys addresses.
		"POST /v1/company/" 								            => ["CompanyController", "create_company"], 		        // TEST - Insert a new company.
		"PUT /v1/company/([\w-]+)" 							            => ["CompanyController", "update_company"], 		        // TEST - Update company.
		"PUT /v1/company/address/([\w-]+)" 							            => ["CompanyController", "update_company_address"], 		        // TEST - Update address of a company.
		"DELETE /v1/company/([\w-]+)"						            => ["CompanyController", "delete_company"], 		        // TEST - Delete company.
		"PUT /v1/company/reactivate/([\w-]+)" 				            => ["CompanyController", "reactivate_company"], 	       	// TEST - Reactivate company.

		// Máquinas
		"GET /v1/machine/" 								            	=> ["MachineController", "get_all_machines"],			        // TEST - Get all machines
		"GET /v1/machine/([\w-]+)" 							            => ["MachineController", "get_machine_by_uuid"], 	        // TEST - Get details from an machine.
		"GET /v1/machine/company/([\w-]+)" 								=> ["MachineController", "get_machine_by_company_uuid"], 	// TEST - Get machines from user.
		"POST /v1/machine/" 								            => ["MachineController", "create_machine"], 		        // TEST - Insert a new machine.
		"POST /v1/machine/photo/upload/" 								            => ["MachineController", "upload_machine_image"], 		        // TEST - Insert a image for a machine.
		"PUT /v1/machine/([\w-]+)" 							            => ["MachineController", "update_machine"], 		        // TEST - Update machine.
		"DELETE /v1/machine/([\w-]+)"						            => ["MachineController", "delete_machine"], 		        // TEST - Delete machine.
		"PUT /v1/machine/reactivate/([\w-]+)" 						=> ["MachineController", "reactivate_machine"], 	       	// TEST - Reactivate machine.
		"GET /v1/machine/params/([\w-]+)" 								            => ["MachineController", "get_machine_by_params"], 		        // TEST - Get the right machines based on the params of the project.

		// Projetos
		"GET /v1/project/" 								            => ["ProjectController", "get_all_projects"],			        // TEST - Get all projects
		"GET /v1/project/([\w-]+)" 							            => ["ProjectController", "get_project_by_uuid"], 	        // TEST - Get details from an project.
		"GET /v1/project/company/([\w-]+)" 							            => ["ProjectController", "get_project_by_company_uuid"], 	        // TEST - Get details from an project.
		"GET /v1/project/budget/([\w-]+)" 							            => ["ProjectController", "get_project_by_budget_uuid"], 	        // TEST - Get details from an project by budget uuid.
		"GET /v1/project/list/company/([\w-]+)" 							=> ["ProjectController", "get_project_list_financial_by_company_uuid"], // TEST - Get project list with just the name, uuid 
		"GET /v1/project/logged/" 									=> ["ProjectController", "get_project_by_logged"], 	// TEST - Get projects from user.
		"GET /v1/project/checks/([\w-]+)" 							            => ["ProjectController", "get_project_checks"], 	        // TEST - Get all check_in and check_out of all the projects of the employee_uuid
		"POST /v1/project/" 								            => ["ProjectController", "create_project_user"], 		        // TEST - Insert a new project of an user.
		"POST /v1/project/client/" 								            => ["ProjectController", "create_project_client"], 		        // TEST - Insert a new project of a client.
		"POST /v1/project/lifting/" 								            => ["ProjectController", "create_project_lifting"], 		        // TEST - Insert a new project.
		"PUT /v1/project/([\w-]+)" 							            => ["ProjectController", "update_project"], 		        // TEST - Update project.
		"DELETE /v1/project/([\w-]+)"						            => ["ProjectController", "delete_project"], 		        // TEST - Delete project.
		"PUT /v1/project/reactivate/([\w-]+)" 				            => ["ProjectController", "reactivate_project"], 	       	// TEST - Reactivate project.

		// Categorias do equipamento (máquinas)
		"GET /v1/machine/category/" 								            => ["MachineCategoryController", "get_all_machine_categories"],			        // TEST - Get all machine categories
		"GET /v1/machine/category/([\w-]+)" 							            => ["MachineCategoryController", "get_machine_category_by_uuid"], 	        // TEST - Get details from an category of and machine.
		"GET /v1/machine/category/group/([\w-]+)" 							            => ["MachineCategoryController", "get_machine_category_by_group_uuid"], 	        // TEST - Get all machine categories based on the group name.
		"GET /v1/machine/category/project/([\w-]+)" 							            => ["MachineCategoryController", "get_machine_category_by_project_category_uuid"], 	        // TEST - Get all machine categories based on the project category name.
		"POST /v1/machine/category/" 								            => ["MachineCategoryController", "create_machine_category"], 		        // TEST - Insert a new machine category.
		"PUT /v1/machine/category/([\w-]+)" 							            => ["MachineCategoryController", "update_machine_category"], 		        // TEST - Update machine category.
		"DELETE /v1/machine/category/([\w-]+)"						            => ["MachineCategoryController", "delete_machine_category"], 		        // TEST - Delete machine category.
		"PUT /v1/machine/category/reactivate/([\w-]+)" 				            => ["MachineCategoryController", "reactivate_machine_category"], 	       	// TEST - Reactivate machine category.

		// Grupo das categorias do equipamento (máquinas)
		"GET /v1/category/group/" 								            => ["MachineCategoryGroupController", "get_all_machine_category_group"],			        // TEST - Get all machine category groups
		"GET /v1/category/group/([\w-]+)" 							            => ["MachineCategoryGroupController", "get_machine_category_group_by_uuid"], 	        // TEST - Get details from a machine category group.
		"GET /v1/category/group/project/([\w-]+)" 							            => ["MachineCategoryGroupController", "get_machine_category_group_by_project_category_uuid"], 	        // TEST - Get all categories groups based on the project category name.
		"POST /v1/category/group/" 								            => ["MachineCategoryGroupController", "create_machine_category_group"], 		        // TEST - Insert a new machine category group.
		"PUT /v1/category/group/([\w-]+)" 							            => ["MachineCategoryGroupController", "update_machine_category_group"], 		        // TEST - Update machine category group.
		"DELETE /v1/category/group/([\w-]+)"						            => ["MachineCategoryGroupController", "delete_machine_category_group"], 		        // TEST - Delete machine category group.
		"PUT /v1/category/group/reactivate/([\w-]+)" 				            => ["MachineCategoryGroupController", "reactivate_machine_category_group"], 	       	// TEST - Reactivate machine category group.

		// Orçamentos
		"GET /v1/budget/logged/" 								            	=> ["BudgetController", "get_budget_by_logged"],			        // TEST - Get all budgets
		// "GET /v1/budget/([\w-]+)/company/([\w-]+)" 							            => ["BudgetController", "get_budget_by_uuid_and_company_uuid"], 	        // TEST - Get details from an budget.
		"GET /v1/budget/([\w-]+)" 							            => ["BudgetController", "get_budget_by_uuid"], 	        // TEST - Get details from an budget.
		"GET /v1/budget/details/([\w-]+)" 								=> ["BudgetController", "get_budget_details_by_uuid"], // Get budget details 
		"GET /v1/budget/project/([\w-]+)" 								=> ["BudgetController", "get_budget_by_project_uuid"], 	// TEST - Get budgets from an project.
		"GET /v1/budget/project/financial/([\w-]+)" 					=> ["BudgetController", "get_budget_financial_by_project_uuid"], 	//
		"GET /v1/budget/company/([\w-]+)" 								=> ["BudgetController", "get_budget_by_company_uuid"], 	// TEST - Get budgets from a company.
		"POST /v1/budget/" 								            	=> ["BudgetController", "create_budget"], 		        // TEST - Insert a new budget.
		"PUT /v1/budget/" 							            => ["BudgetController", "update_budget"], 		        // TEST - Update budget.
		"DELETE /v1/budget/([\w-]+)"						            => ["BudgetController", "delete_budget"], 		        // TEST - Delete budget.
		"PUT /v1/budget/reactivate/([\w-]+)" 				            => ["BudgetController", "reactivate_budget"], 	       	// TEST - Reactivate budget.
		"POST /v1/budget/available/" 							            => ["BudgetController", "get_available_company"], 	        // Get the companies that has the machine available based on the date and hour chosen
		"PUT /v1/budget/accept/([\w-]+)"						            => ["BudgetController", "accept_budget"],		// Update the expected_date of the budget.
		"DELETE /v1/budget/cancel/([\w-]+)"						            => ["BudgetController", "cancel_budget"], // User cancel the budget.

		// Orçamentos
		"GET /v1/budget/machine/operator/([\w-]+)"													=> ["BudgetMachineOperatorController", "get_budget_machine_operator_by_uuid"], 	        // TEST - Get details from an budget.
		"GET /v1/budget/machine/logged/"													=> ["BudgetMachineOperatorController", "get_budget_by_logged"], 	        // TEST - Get details from an budget.
		"GET /v1/budget/machine/logged/checkin/"													=> ["BudgetMachineOperatorController", "get_budget_has_checkin"], 	        // TEST - Get list of projects that has checkin.
		"POST /v1/budget/machine/operator/"												=> ["BudgetMachineOperatorController", "create_budget_machine_operator"], 		        // TEST - Insert a list of operators in a machine.
		"PUT /v1/budget/machine/operator/([\w-]+)"									=> ["BudgetMachineOperatorController", "update_budget_machine_operator"], 		        // TEST - Update the machine of an operator.
		"DELETE /v1/budget/machine/operator/([\w-]+)"							=> ["BudgetMachineOperatorController", "delete_budget_machine_operator"], 		        // TEST - Delete a relationship between a machine and operator.

		// Taxas e afins de um orçamento
		"POST /v1/budget/service/charge/" 								            => ["BudgetServiceChargeController", "create_budget_service_charge"], 		        // TEST - Insert a list of service charges a one budget.
		"PUT /v1/budget/service/charge/([\w-]+)"									=> ["BudgetServiceChargeController", "update_budget_service_charge"], 		        // TEST - Update the type of service charge of a budget.
		"DELETE /v1/budget/service/charge/([\w-]+)"							=> ["BudgetServiceChargeController", "delete_budget_service_charge"], 		        // TEST - Delete a relationship between a service charge and a budget.

		// Funcionários
		"GET /v1/employee/" 								            => ["EmployeeController", "get_employees"],			        // TEST - Get all projects
		"GET /v1/employee/([\w-]+)" 							            => ["EmployeeController", "get_employee_by_uuid"], 	        // TEST - Get details from an employee.
		"GET /v1/employee/invite/" 							            => ["EmployeeController", "get_invite_by_logged_user"], 	        // TEST - Get details from an employee.
		"GET /v1/employee/logged/" 							            => ["EmployeeController", "get_employee_by_logged"], 	        // TEST - Get employees profile by user logged.
		"GET /v1/employee/company/([\w-]+)" 							            => ["EmployeeController", "get_employee_by_company_uuid"], 	        // TEST - Get details from an employee.
		"POST /v1/employee/" 								            => ["EmployeeController", "create_employee"], 		        // TEST - Insert a new employee.
		"PUT /v1/employee/([\w-]+)" 							            => ["EmployeeController", "update_employee"], 		        // TEST - Update employee.
		"DELETE /v1/employee/([\w-]+)"						            => ["EmployeeController", "delete_employee"], 		        // TEST - Delete employee.
		"PUT /v1/employee/reactivate/([\w-]+)" 				            => ["EmployeeController", "reactivate_employee"], 	       	// TEST - Reactivate employee.
		"PUT /v1/employee/invite/accept/([\w-]+)" 							            => ["EmployeeController", "accept_invite"], 		        // TEST - Update employee.
		"DELETE /v1/employee/invite/decline/([\w-]+)"						            => ["EmployeeController", "decline_invite"], 		        // TEST - Delete employee.

		// // Status do projeto
		"GET /v1/status/" 									        => ["StatusController", "get_status"],			        // TEST - Get all status
		"GET /v1/status/([\w-]+)" 							            => ["StatusController", "get_status_by_uuid"], 	        // TEST - Get details from an status.
		"POST /v1/status/" 								            => ["StatusController", "create_status"], 		        // TEST - Insert a new status.
		"PUT /v1/status/([\w-]+)" 							            => ["StatusController", "update_status"], 		        // TEST - Update status.
		"DELETE /v1/status/([\w-]+)"							            => ["StatusController", "delete_status"], 		        // TEST - Delete status.
		"PUT /v1/status/reactivate/([\w-]+)" 				            => ["StatusController", "reactivate_status"], 	       	// TEST - Reactivate status.

		// Histórico do orçamento
		"GET /v1/budget/history/" 								            => ["BudgetHistoryController", "get_all_budgets_histories"],			        // TEST - Get all budgets
		"GET /v1/budget/history/([\w-]+)" 							            => ["BudgetHistoryController", "get_history_by_uuid"], 	        // TEST - Get details from a history budget.
		"GET /v1/budget/history/budget/([\w-]+)" 							            => ["BudgetHistoryController", "get_budget_history_by_budget_uuid"], 	        // TEST - Get history from a budget.
		"POST /v1/budget/history/" 								            => ["BudgetHistoryController", "create_budget_history"], 		        // TEST - Insert a new history in a budget.
		"PUT /v1/budget/history/([\w-]+)" 							            => ["BudgetHistoryController", "update_budget"], 		        // TEST - Update budget history.
		"DELETE /v1/budget/history/([\w-]+)"						            => ["BudgetHistoryController", "delete_budget_history"], 		        // TEST - Delete budget history.
		"PUT /v1/budget/history/reactivate/([\w-]+)" 				            => ["BudgetHistoryController", "reactivate_budget_history"], 	       	// TEST - Reactivate budget.

		// Certificações dos funcionários
		"GET /v1/employee/certification/" 								            => ["EmployeeCertificationController", "get_all_employee_certification"],			        // TEST - Get all projects
		"GET /v1/employee/certification/([\w-]+)" 							    => ["EmployeeCertificationController", "get_certification_by_employee_uuid"], 	        // TEST - Get details from an employee.
		"GET /v1/certification/([\w-]+)" 							           			 => ["EmployeeCertificationController", "get_certification_by_uuid"], 	        // TEST - Get details from an employee.
		"POST /v1/employee/certification/" 								            => ["EmployeeCertificationController", "create_employee_certification"], 		        // TEST - Insert a new employee.
		"PUT /v1/employee/certification/([\w-]+)" 							            => ["EmployeeCertificationController", "update_employee_certification"], 		        // TEST - Update employee.
		"DELETE /v1/employee/certification/([\w-]+)"						            => ["EmployeeCertificationController", "delete_employee_certification"], 		        // TEST - Delete employee.
		"PUT /v1/employee/certification/reactivate/([\w-]+)" 				            => ["EmployeeCertificationController", "reactivate_employee_certification"], 	       	// TEST - Reactivate employee.
		"POST /v1/certification/upload/([\w-]+)" 								            => ["EmployeeCertificationController", "upload_certification_file"], 		        // TEST - Insert a new employee.

		// Orçamentos - proposta
		// "GET /v1/budget/proposal/logged/" 								            	=> ["BudgetController", "get_budget_by_logged"],			        // TEST - Get all budgets
		// "GET /v1/budget/proposal/([\w-]+)" 							            => ["BudgetProposalController", "get_budget_by_uuid"], 	        // TEST - Get details from an budget.
		// "GET /v1/budget/proposal/([\w-]+)" 								=> ["BudgetProposalController", "get_budget_proposal_by_uuid"], 	// TEST - Get budgets from an project.
		// "GET /v1/budget/proposal/details/([\w-]+)" 								=> ["BudgetProposalController", "get_proposal_details_by_budget_proposal_uuid"], 	// TEST - Get details from company, machine, proposal and prices.
		// "GET /v1/budget/proposal/budget/([\w-]+)" 								=> ["BudgetProposalController", "get_budget_proposal_by_budget_uuid"], 	// TEST - Get budgets from a company.
		// "GET /v1/budget/proposal/project/([\w-]+)" 								=> ["BudgetProposalController", "get_budget_proposal_by_project_uuid"], 	// TEST - Get proposal of a project.
		// "POST /v1/budget/proposal/" 								            => ["BudgetProposalController", "create_budget_proposal"], 		        // TEST - Insert a new budget.
		// "PUT /v1/budget/proposal/([\w-]+)" 							            => ["BudgetProposalController", "update_budget_proposal"], 		        // TEST - Update budget.
		// "DELETE /v1/budget/proposal/cancel/([\w-]+)"						            => ["BudgetProposalController", "cancel_budget_proposal"], 		        // TEST - Delete budget.
		// "PUT /v1/budget/proposal/accept/([\w-]+)"						            => ["BudgetProposalController", "accept_budget_proposal"], 		        // TEST - Accept the proposal by proposal_uuid.
		// "PUT /v1/budget/proposal/reactivate/([\w-]+)" 				            => ["BudgetProposalController", "reactivate_budget_proposal"], 	       	// TEST - Reactivate budget.

		// Relacionamento entre operadores e orçamentos
		"GET /v1/operator/budget/([\w-]+)" 							            => ["OperatorBudgetController", "get_operators_by_budget_uuid"], 	        // TEST - Get operators of a budget
		"GET /v1/operator/budget/project/([\w-]+)" 								=> ["OperatorBudgetController", "get_operators_by_project_uuid"], 	// TEST - Get operators of a project
		"GET /v1/operator/budget/logged/" 							            => ["OperatorBudgetController", "get_projects_by_operator_logged"], 	        // TEST - Get projects of an operator logged
		"GET /v1/operator/budget/projects/([\w-]+)" 							            => ["OperatorBudgetController", "get_projects_by_operator_uuid"], 	        // TEST - Get projects of an operator based on operator_uuid.
		"POST /v1/operator/budget/" 								            => ["OperatorBudgetController", "create_operator_budget"], 		        // TEST - Insert a new operator in a budget.
		"DELETE /v1/operator/budget/([\w-]+)"						            => ["OperatorBudgetController", "delete_operator_budget"], 		        // TEST - Delete the operator of a budget.

		// Operadores
		"GET /v1/operator/([\w-]+)" 							            => ["OperatorController", "get_operator_by_company_uuid"], 	        // TEST - Get all operators from a company
		"GET /v1/operator/company/([\w-]+)" 							            			=> ["OperatorController", "get_operator_by_company"], 	        // Get all operators from a company based on the consultant that are logged.
		"GET /v1/operator/checks/([\w-]+)" 							            => ["OperatorController", "get_operator_checks"], 	        // TEST - Get all check_in and check_out of all the projects of the employee_uuid
		// Checkin dos operadores
		"POST /v1/machine/operator/checkin/"												=> ["OperatorCheckInController", "create_operator_check_in"], 		        // TEST - Insert a check in of an operator of the machine.

		// Checkou dos operadores
		"POST /v1/machine/operator/checkout/"												=> ["OperatorCheckOutController", "create_operator_check_out"], 		        // TEST - Insert a check out of an operator of the machine.


		// Categorias do projeto (Terraplanagem / Demolição, Elevação e Outros)
		"GET /v1/project/category/" 								            => ["ProjectCategoryController", "get_all_project_categories"],			        // TEST - Get all project categories
		"GET /v1/project/category/([\w-]+)" 							            => ["ProjectCategoryController", "get_project_category_by_uuid"], 	        // TEST - Get details from an category of and project.
		"POST /v1/project/category/" 								            => ["ProjectCategoryController", "create_project_category"], 		        // TEST - Insert a new project category.
		"PUT /v1/project/category/([\w-]+)" 							            => ["ProjectCategoryController", "update_project_category"], 		        // TEST - Update project category.
		"DELETE /v1/project/category/([\w-]+)"						            => ["ProjectCategoryController", "delete_project_category"], 		        // TEST - Delete project category.
		"PUT /v1/project/category/reactivate/([\w-]+)" 				            => ["ProjectCategoryController", "reactivate_project_category"], 	       	// TEST - Reactivate project category.

		// Máquinas de um orçamento
		"GET /v1/budget/machine/" 								            	=> ["BudgetMachineController", "get_budget_by_logged"],			        // TEST - Get all budgets
		"GET /v1/budget/machine/([\w-]+)" 							            => ["BudgetMachineController", "get_budget_machine_by_uuid"], 	        // TEST - Get details from an budget.
		"GET /v1/budget/machine/project/([\w-]+)" 								=> ["BudgetMachineController", "get_budget_machine_by_project_uuid"], 	// TEST - Get budgets from an project.
		"POST /v1/budget/machine/" 								            => ["BudgetMachineController", "create_budget_machine"], 		        // TEST - Insert a new budget.
		"PUT /v1/budget/machine/([\w-]+)" 							            => ["BudgetMachineController", "update_budget_machine"], 		        // TEST - Update budget.
		// "PUT /v1/budget/machine/checkin/([\w-]+)" 							            => ["BudgetMachineController", "create_machine_check_in"], 		        // TEST - Update budget.
		// "PUT /v1/budget/machine/checkout/([\w-]+)" 							            => ["BudgetMachineController", "create_machine_check_out"], 		        // TEST - Update budget.
		"PUT /v1/budget/machine/price/([\w-]+)" 							            => ["BudgetMachineController", "update_budget_machine_price"], 		        // TEST - Update budget machine values of price per hour and por distance.
		"DELETE /v1/budget/machine/([\w-]+)"						            => ["BudgetMachineController", "delete_budget_machine"], 		        // TEST - Delete budget.

		// Categorias do projeto (Terraplanagem / Demolição, Elevação e Outros)
		"GET /v1/project/checklist/([\w-]+)" 								            => ["ProjectChecklistController", "get_project_checklist_by_uuid"],			        // TEST - Get all project categories
		"GET /v1/project/checklist/budget/([\w-]+)" 							            => ["ProjectChecklistController", "get_project_checklist_by_budget_uuid"], 	        // TEST - Get details from an category of and project.
		"POST /v1/project/checklist/" 								            => ["ProjectChecklistController", "create_project_checklist"], 		        // TEST - Insert a new project category.
		"PUT /v1/project/checklist/([\w-]+)" 							            => ["ProjectChecklistController", "update_project_category"], 		        // TEST - Update project category.
		"PUT /v1/project/checklist/verified/([\w-]+)" 							            => ["ProjectChecklistController", "update_verified_project_checklist"], 		        // TEST - Update project category.
		"DELETE /v1/project/checklist/([\w-]+)"						            => ["ProjectChecklistController", "delete_project_checklist"], 		        // TEST - Delete project category.
		"PUT /v1/project/checklist/reactivate/([\w-]+)" 				            => ["ProjectChecklistController", "reactivate_project_checklist"], 	       	// TEST - Reactivate project category.

		// Tipos de taxas, documentos e etc
		"GET /v1/service/charge/([\w-]+)" 								            				=> ["ServiceChargeController", "get_service_charge_by_uuid"],			        // Get one service charge by uuid.
		"GET /v1/service/charge/company/([\w-]+)" 								            => ["ServiceChargeController", "get_service_charge_by_company_uuid"],			        // Get all type of charges of a company.
		"POST /v1/service/charge/" 								            							=> ["ServiceChargeController", "create_service_charge"], 		        // Insert a new type of charge.
		"PUT /v1/service/charge/([\w-]+)" 								            				=> ["ServiceChargeController", "update_service_charge"],			        // Update service charge by uuid.
		"DELETE /v1/service/charge/([\w-]+)" 								            		=> ["ServiceChargeController", "delete_service_charge"],			        //Delete service charge by uuid.

		// Clientes
		"GET /v1/client/company/([\w-]+)" 					                    => ["ClientController", "get_clients_by_company_uuid"], 	        // TEST - Get all clients of a company.
		"GET /v1/client/([\w-]+)" 					                    => ["ClientController", "get_client_by_uuid"], 	        // TEST - Get details from a client.
		"POST /v1/client/" 						                    => ["ClientController", "create_client"], 		        // TEST - Insert a new client.
		"PUT /v1/client/([\w-]+)" 					                    => ["ClientController", "update_client"], 		        // TEST - Update client.
		"DELETE /v1/client/([\w-]+)"					                    => ["ClientController", "delete_client"], 		        // TEST - Delete client.
		"PUT /v1/client/reactivate/([\w-]+)" 		                    => ["ClientController", "reactivate_client"], 	        // TEST - Reactivate client.

		// Tipos de taxas, documentos e etc
		"GET /v1/budget/payment/([\w-]+)" 								            				=> ["BudgetPaymentController", "get_budget_payment_by_uuid"],			        // Get one service charge by uuid.
		"GET /v1/budget/payment/budget/([\w-]+)" 								            => ["BudgetPaymentController", "get_budget_payment_by_budget_uuid"],			        // Get all type of charges of a company.
		"POST /v1/budget/payment/" 								            							=> ["BudgetPaymentController", "create_budget_payment"], 		        // Insert a new type of charge.
		"PUT /v1/budget/payment/([\w-]+)" 								            				=> ["BudgetPaymentController", "update_budget_payment"],			        // Update service charge by uuid.
		"DELETE /v1/budget/payment/([\w-]+)" 								            		=> ["BudgetPaymentController", "delete_budget_payment"],			        //Delete service charge by uuid.

		// PDF do orçamento
		"GET /v1/budget/pdf/([\w-]+)"																	=> ["BudgetPdfController", "generate_pdf"],		// Generate budget pdf with budget_uuid

		// Tipos de adicionais de uma empresa
		"GET /v1/additional/type/([\w-]+)" 								            				=> ["AdditionalTypeController", "get_additional_type_by_uuid"],			        // Get one addtional type by uuid.
		"GET /v1/additional/type/company/([\w-]+)" 								            => ["AdditionalTypeController", "get_additional_type_by_company_uuid"],			        // Get all type of additionals of a company.
		"POST /v1/additional/type/" 								            							=> ["AdditionalTypeController", "create_additional_type"], 		        // Insert a new type of additional.
		"PUT /v1/additional/type/([\w-]+)" 								            				=> ["AdditionalTypeController", "update_additional_type"],			        // Update addtional type by uuid.
		"DELETE /v1/additional/type/([\w-]+)" 								            		=> ["AdditionalTypeController", "delete_additional_type"],			        //Delete addtional type by uuid.

		// Tipos de adicionais de uma maquina
		"GET /v1/machine/additional/([\w-]+)" 								            				=> ["MachineAdditionalController", "get_machine_additional_by_uuid"],			        // Get one additional of a machine by uuid.
		"GET /v1/machine/additional/machine/([\w-]+)" 								            => ["MachineAdditionalController", "get_machine_additional_by_machine_uuid"],			        // Get all additionals of a machine.
		"GET /v1/machine/additional/company/([\w-]+)" 								            => ["MachineAdditionalController", "get_machine_additional_by_company_uuid"],			        // Get all additionals of a machine.
		"POST /v1/machine/additional/" 								            							=> ["MachineAdditionalController", "create_machine_additional"], 		        // Insert a new additional in a machine.
		"DELETE /v1/machine/additional/([\w-]+)" 								            		=> ["MachineAdditionalController", "delete_machine_additional"],			        //Delete additional of a machine by uuid.

		// Tipos de adicionais de um operator
		"GET /v1/operator/additional/([\w-]+)" 								            				=> ["OperatorAdditionalController", "get_operator_additional_by_uuid"],			        // Get one additional of an operator by uuid.
		"GET /v1/operator/additional/operator/([\w-]+)" 								            => ["OperatorAdditionalController", "get_operator_additional_by_operator_uuid"],			        // Get all additionals of an operator.
		"GET /v1/operator/additional/company/([\w-]+)" 								            => ["OperatorAdditionalController", "get_operator_additional_by_company_uuid"],			        // Get all additionals of an operator.
		"POST /v1/operator/additional/" 								            							=> ["OperatorAdditionalController", "create_operator_additional"], 		        // Insert a new additional in an operator.
		"DELETE /v1/operator/additional/([\w-]+)" 								            		=> ["OperatorAdditionalController", "delete_operator_additional"],			        //Delete additional of an operator by uuid.

	];
