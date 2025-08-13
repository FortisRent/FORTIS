<?php
	require_once './vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	
	require_once './db/db.php';
	require_once './routes/router.php';
	require_once './controller/onboard/onboard_controller.php';						    // Entrada
	require_once './controller/onboard/login_controller.php';						    // Login (tokens)
	require_once './controller/onboard/user_controller.php';						    // Usuários
	require_once './controller/onboard/user_address_controller.php';					// Usuario (cadastro de endereços) 
	require_once './controller/password/password_controller.php'; 					    // Resetar senha
	require_once './controller/selects/country_controller.php';						    // Países
	require_once './controller/selects/state_controller.php';				            // Estados
	require_once './controller/selects/city_controller.php';					        // Cidades
	require_once './controller/selects/payment_method_controller.php';				    // Métodos de pagamento.

	// Manter para caso seja necessário utilizar como base para cargos, avaliações e propaganda.
	require_once './controller/role/role_controller.php';								// Cargos (cadastro de cargos)
	require_once './controller/review/review_controller.php';					        // Avaliações (reviews).
	require_once './controller/role/role_document_controller.php';						// Documents (cadastro)
	require_once './controller/onboard/leads_controller.php';			          		// Inscrições da landing page (pré-lançamento) 

	require_once './controller/payment/payment_controller.php';							// Registro de pagamentos
	require_once './controller/company/company_controller.php';							// Registro das empresas
	require_once './controller/machine/machine_controller.php';							// Controlador das máquinas				
	require_once './controller/project/project_controller.php';							// Registro dos projetos
	require_once './controller/machine/machine_category_controller.php';				// Categorias das máquinas
	require_once './controller/machine/machine_category_group_controller.php';			// Grupos de categorias das máquinas
	require_once './controller/budget/budget_controller.php';							// Orçamentos 
	require_once './controller/employee/employee_controller.php';						// Funcionários
	require_once './controller/status/status_controller.php';							// Status de um histórico
	require_once './controller/budget/budget_history_controller.php';					// Histórico dos orçamentos
	require_once './controller/employee/employee_certification_controller.php';			// Certificações do operador/funcionário
	require_once './controller/budget/budget_proposal_controller.php';					// Propostas para um orçamento
	require_once './controller/operator/operator_budget_controller.php';				// 
	require_once './controller/operator/operator_controller.php';						// 
	require_once './controller/project/project_category_controller.php';				// Tipos de projetos
	require_once './controller/budget/budget_machine_controller.php';					// Máquinas que foram pedidas para orçamento (relação de muitos para muitos)
	require_once './controller/project/project_checklist_controller.php'; 				// Checklist de cada projeto
	require_once './controller/budget/budget_machine_operator_controller.php';			// Relacionamento entre vários operadores para muitas máquinas.
	require_once './controller/budget/budget_service_charge_controller.php';			// Relacionar tipos de taxas, tipos de documentos e afins para um orçamento.
	require_once './controller/service_charge/service_charge_controller.php';			// Controlados dos tipos de taxas, tipos de documentos e etc.
	require_once './controller/operator/operator_check_in_controller.php';				// Check in do funcionário (operador) conectado a uma máquina.
	require_once './controller/operator/operator_check_out_controller.php';				// Check out do funcionário (operador) conectado a uma máquina.
	require_once './controller/client/client_controller.php';							// Clientes de uma empresa.
	require_once './controller/budget/budget_payment_controller.php';					// Registro financeiro de cada orçamento.
	require_once './pdfa/budget_pdf.php';												// Gerador do pdf do orçamento.
	require_once './controller/financial/omie_controller.php';						// Omie API Controller
	require_once './controller/additional_type/additional_type_controller.php';			// Controlador dos tipos de adicionais de uma empresa.
	require_once './controller/machine/machine_additional_controller.php';  // Relacionamento entre as máquinas e adicionais.
	require_once './controller/operator/operator_additional_controller.php';  // Relacionamento entre os operadores e adicionais.

	// DB Pool connection
	$mysqli = DBConnection::connect();
	
	// Setting default hour.
	date_default_timezone_set('America/Sao_Paulo');
	$mysqli->query("SET time_zone = '-03:00'");

	// Setting LC_TIME_NAMES to Portuguese (Brazil)
	$mysqli->query("SET lc_time_names = 'pt_BR'");

	header("Content-Type: application/json");
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, token");
	
	// Check if it's an OPTIONS request (preflight) and force responce with a 200 OK ----> I F*cking hate CORS!!!!
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		http_response_code(200);
		exit();
	}

	$onboard_controller 						= new OnboardController($mysqli);
	$login_controller 			   				= new LoginController($mysqli);
	$user_controller 							= new UserController($mysqli);
	$password_controller						= new PasswordController($mysqli);
	$country_controller 						= new CountryController($mysqli);
	$payment_method_controller					= new PaymentMethodController($mysqli);
	$state_controller 							= new StateController($mysqli);
	$city_controller 							= new CityController($mysqli);
	$role_controller 							= new RoleController($mysqli);
	$user_address_controller					= new UserAddressController($mysqli);
	$review_controller						    = new ReviewController($mysqli);
	// $document_type_controller		        = new DocumentTypeController($mysqli);
	// $log_stage_controller					= new LogStageController($mysqli);	
	$role_document_controller					= new RoleDocumentController($mysqli);
	$leads_controller 			                = new LeadController($mysqli);
	$payment_controller							= new PaymentController($mysqli);
	$company_controller							= new CompanyController($mysqli);
	$machine_controller							= new MachineController($mysqli);
	$project_controler 							= new ProjectController($mysqli);
	$machine_category_controller				= new MachineCategoryController($mysqli);
	$machine_category_group_controller			= new MachineCategoryGroupController($mysqli);
	$budget_controller							= new BudgetController($mysqli);
	$employee_controller						= new EmployeeController($mysqli);
	$status_controller							= new StatusController($mysqli);
	$budget_history_controller					= new BudgetHistoryController($mysqli);
	$employee_certification_controller			= new EmployeeCertificationController($mysqli);
	$budget_proposal_controller					= new BudgetProposalController($mysqli);
	$operator_budget_controller					= new OperatorBudgetController($mysqli);
	$operator_controller 						= new OperatorController($mysqli);
	$project_category_controller				= new ProjectCategoryController($mysqli);
	$budget_machine_controller					= new BudgetMachineController($mysqli);
	$project_checklist_controller				= new ProjectChecklistController($mysqli);
	$budget_machine_operator_controller			= new BudgetMachineOperatorController($mysqli);
	$budget_service_charge_controller			= new BudgetServiceChargeController($mysqli);
	$service_charge_controller					= new ServiceChargeController($mysqli);
	$operator_check_in_controller				= new OperatorCheckInController($mysqli);
	$operator_check_out_controller				= new OperatorCheckOutController($mysqli);
	$client_controller							= new ClientController($mysqli);
	$budget_payment_controller					= new BudgetPaymentController($mysqli);
	$budget_pdf_controller						= new BudgetPdfController($mysqli);
	$omie_controller							= new OmieController();
	$additional_type_controller				= new AdditionalTypeController($mysqli);
	$machine_additional_controller 			= new MachineAdditionalController($mysqli);
	$operator_additional_controller 			= new OperatorAdditionalController($mysqli);

	$controllers = [
		"OnboardController" 					=> $onboard_controller,					
		"LoginController" 						=> $login_controller,					
		"UserController" 						=> $user_controller,					
		"PasswordController"					=> $password_controller,				
		"CountryController" 					=> $country_controller,					
		"PaymentMethodController"				=> $payment_method_controller,			
		"StateController"						=> $state_controller,					
		"CityController"						=> $city_controller,					
		"RoleController"						=> $role_controller,					  
		"UserAddressController"					=> $user_address_controller,			
		"ReviewController"					    => $review_controller,				   
		// "DocumentTypeController"			    => $document_type_controller,	
		// "LogStageController"					=> $log_stage_controller,
		"RoleDocumentController"				=> $role_document_controller,
		"LeadController"		                => $leads_controller,
		"PaymentController"		                => $payment_controller,
		"CompanyController"						=>$company_controller,
		"MachineController"						=>$machine_controller,
		"ProjectController"						=>$project_controler,
		"MachineCategoryController"				=>$machine_category_controller,
		"MachineCategoryGroupController"		=>$machine_category_group_controller,
		"BudgetController"						=>$budget_controller,
		"EmployeeController"					=>$employee_controller,
		"StatusController"						=>$state_controller,
		"BudgetHistoryController"				=>$budget_history_controller,
		"EmployeeCertificationController"		=>$employee_certification_controller,
		"BudgetProposalController"				=>$budget_proposal_controller,
		"OperatorBudgetController"				=>$operator_budget_controller,
		"OperatorController"					=>$operator_controller,
		"ProjectCategoryController"				=>$project_category_controller,
		"BudgetMachineController"				=>$budget_machine_controller,
		"ProjectChecklistController"			=> $project_checklist_controller,
		"BudgetMachineOperatorController"		=>$budget_machine_operator_controller,
		"BudgetServiceChargeController"			=>$budget_service_charge_controller,
		"ServiceChargeController"				=>$service_charge_controller,
		"OperatorCheckInController"				=>$operator_check_in_controller,
		"OperatorCheckOutController"			=>$operator_check_out_controller,
		"ClientController"						=>$client_controller,
		"BudgetPaymentController"				=>$budget_payment_controller,
		"BudgetPdfController"					=>$budget_pdf_controller,
		"OmieController"						=>$omie_controller,
		"AdditionalTypeController"			=>$additional_type_controller,
		"MachineAdditionalController" =>$machine_additional_controller,
		"OperatorAdditionalController" =>$operator_additional_controller,
	];

	$method = $_SERVER["REQUEST_METHOD"];
	$uri = $_SERVER["REQUEST_URI"];
	$route = "$method $uri";

	// Se for um arquivo estático, retorna a imagem pedida.
	if (strpos($_SERVER['REQUEST_URI'], '/uploads/profile/') === 0) {
		$file = __DIR__ . $_SERVER['REQUEST_URI'];

		if (file_exists($file) && is_file($file)) {
			header('Content-Type: image/jpeg');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit();
		}
	}

	if (strpos($_SERVER['REQUEST_URI'], '/uploads/clinic/') === 0) {
		$file = __DIR__ . $_SERVER['REQUEST_URI'];

		if (file_exists($file) && is_file($file)) {
			header('Content-Type: image/jpeg');
			header('Content-Length: ' . filesize($file));

			readfile($file);

			exit();
		}
	}

	// Se for da API, retorna a rota requisitada.
	foreach ($routes as $pattern => $controllerMethod) {
		if (preg_match("~^$pattern$~", $route, $matches)) {
			$controllerName = $controllerMethod[0];
			$method = $controllerMethod[1];

			if (isset($controllers[$controllerName]) && method_exists($controllers[$controllerName], $method)) {
				$arguments = array_slice($matches, 1);
				$controllers[$controllerName]->$method(...$arguments);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Internal Server Error - Index."]);
			}
			exit();
		}
	}

	// Se chegou aqui, é pq não encontrou a rota ou deu ruim em algum método.
	http_response_code(404);
	echo json_encode(["error" => "Not Found"]);

	// Fecha a conexão com o mysql.
	$mysqli->close();