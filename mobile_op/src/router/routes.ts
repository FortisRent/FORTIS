import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
	{
		path: '/',
		component: () => import('layouts/MenuNotLogged.vue'),
		children: [
			{ path: '',				  					component: () => import('pages/onboard/SplashScreen.vue') },
			{ path: 'login',			  				component: () => import('pages/onboard/login/LoginView.vue') },
			{ path: 'register',							component: () => import('pages/onboard/user/UserCreate.vue') },
			{ path: 'logout',							component: () => import('pages/onboard/Logout.vue') },
			{ path: 'password/email',					component: () => import('pages/onboard/password/ValidateEmail.vue') },
			{ path: 'password/code',					component: () => import('pages/onboard/password/ValidateToken.vue') },
			{ path: 'password/update',					component: () => import('pages/onboard/password/UpdatePassword.vue') },
			{ path: 'terms',							component: () => import('src/pages/Meet_references/UserTerms.vue') }, 	
			{ path: 'policy',							component: () => import('src/pages/Meet_references/UserPolicy.vue') }, 	
			{ path: 'lp',								component: () => import('pages/onboard/LP.vue') },
			{ path: 'gps',								component: () => import('pages/GPS.vue') },
		],
	},
	{
		path: '/dashboard',
		component: () => import('layouts/DashboardView.vue'),
		children: [
			{ path: ':company_uuid',					component: () => import('components/DashboardComponents/TableProjects.vue') },
			{ path: 'project/details/:budget_uuid',		component: () => import('components/DashboardComponents/ProjectDetailsDash.vue') },
			{ path: 'history/:company_uuid',			component: () => import('components/DashboardComponents/HistoryDashboard.vue') },
			{ path: 'finance/:company_uuid',			component: () => import('components/DashboardComponents/FinanceDashboard.vue') },
			{ path: 'finance/taxas/:company_uuid',		component: () => import('components/DashboardComponents/TaxaInsert.vue') },
		],
	},
	{
		path: '/user',
		component: () => import('layouts/UserTabs.vue'),
		children: [
			// Usuários
			{ path: 'profile',							component: () => import('src/pages/user_profile/UserProfile.vue') }, 	
			{ path: 'service',							component: () => import('pages/onboard/ServiceNav.vue') }, 	
			{ path: 'change',							component: () => import('pages/onboard/ChangePerfil.vue') }, 	
			{ path: 'manage',							component: () => import('src/pages/user_profile/UserManage.vue') },
			{ path: 'manage/invite',					component: () => import('src/pages/user_profile/UserInvite.vue') },
			{ path: 'manage/info',						component: () => import('src/pages/user_profile/UserManageInfo.vue') },
			{ path: 'manage/info/addaddress',			component: () => import('src/pages/user_profile/UserInfoAddAddress.vue') },
			{ path: 'manage/edit/data',					component: () => import('src/pages/user_profile/UserEditData.vue') },
			{ path: 'manage/edit/address/:user_address_uuid', component: () => import('src/pages/user_profile/UserEditAddress.vue') },
			// { path: 'manage/edit/otheraddress',			component: () => import('src/pages/user_profile/UserEditOtherAddress.vue') },
			{ path: 'manage/contact',					component: () => import('src/pages/user_profile/UserManageContact.vue') },
			{ path: 'manage/edit/phone',				component: () => import('src/pages/user_profile/UserEditContactPhone.vue') },
			{ path: 'manage/edit/emergency',			component: () => import('src/pages/user_profile/UserEditContactEmergency.vue') },
			{ path: 'manage/edit/addemergency',			component: () => import('src/pages/user_profile/UserEditContactEmergency.vue') },
			{ path: 'manage/config/',					component: () => import('src/pages/user_profile/UserConfig.vue') },
			{ path: 'timeline/list',					component: () => import('src/pages/onboard/ServiceProgress.vue') },

			
			// Perfil Colaborador
			{ path: 'manage/profilechange',								component: () => import('src/pages/user_profile/UserManageProfileChange.vue') },
			{ path: 'manage/employee/:employee_uuid',					component: () => import('src/pages/change_profile/EmployeeProfile.vue') },
			{ path: 'manage/employee/documents/:employee_uuid',			component: () => import('src/pages/change_profile/EmployeeEdit.vue') },
			{ path: 'manage/employee/certificate/:employee_uuid',		component: () => import('src/pages/change_profile/EmployeeCertificate.vue') },
			{ path: 'manage/employee/service/:employee_uuid',			component: () => import('src/pages/change_profile/EmployeeService.vue') },
			{ path: 'manage/employee/certificate/create/:employee_uuid',component: () => import('src/pages/change_profile/EmployeeCertificateCreate.vue') },
			
			// teste para pq tem o company uuid 
			
			{ path: 'company',											component: () => import('pages/company/CompanyList.vue') },
			{ path: 'company/options/:company_uuid',					component: () => import('pages/company/CompanyOptions.vue') },
			{ path: 'company/insert',									component: () => import('pages/company/CompanyInsert.vue') },
			{ path: 'company/edit/:company_uuid',						component: () => import('pages/company/CompanyEdit.vue') },
			{ path: 'company/equipments/:company_uuid',					component: () => import('pages/company/CompanyEquipments.vue') },
			{ path: 'company/collaborators/:company_uuid',				component: () => import('pages/company/CompanyCollaborators.vue') },
			{ path: 'company/collaborators/:company_uuid/create',		component: () => import('pages/company/CompanCollaboratorsCreate.vue') },
			{ path: 'company/projects/:company_uuid',					component: () => import('pages/company/CompanyProjects.vue') },
			{ path: 'company/budgets',									component: () => import('pages/company/CompanyBudgets.vue') },
			
			{ path: 'machine/list/:company_uuid',		component: () => import('pages/machine/MachineList.vue') },
			{ path: 'machine/list/:company_uuid',		component: () => import('pages/machine/MachineList.vue') },
			{ path: 'machine/insert/:company_uuid',		component: () => import('pages/machine/MachineInsert.vue') },
			{ path: 'machine/edit/:machine_uuid',		component: () => import('pages/machine/MachineEdit.vue') },
			
			{ path: 'budget/',							component: () => import('src/pages/projects/ServiceBudget.vue') },
			
			// criando projeto  	
			{ path: 'budget/yellow/:project_category_uuid',		component: () => import('src/pages/projects/ServiceBudgetYellow.vue') },
			{ path: 'budget/other/:project_category_uuid',		component: () => import('src/pages/projects/ServiceBudgetOther.vue') }, 	
			{ path: 'budget/lifting/:project_category_uuid',	component: () => import('src/pages/projects/ServiceBudgetLifting.vue') }, 
			
			// selecionando empresa/máquina
			{ path: 'project/recomend/:project_uuid',			component: () => import('src/pages/projects/ProjectRecomend.vue') }, 	

			{ path: 'project/budget/:budget_uuid/company/:company_uuid',	component: () => import('src/pages/projects/ProjectBudget.vue') }, 	
			{ path: 'project/history/:project_uuid',						component: () => import('src/pages/projects/ProjectHistory.vue') }, 	
			{ path: 'project/history/:project_uuid',						component: () => import('src/pages/projects/ProjectHistory.vue') }, 	
			{ path: 'project/history/:project_uuid',						component: () => import('src/pages/projects/ProjectHistory.vue') }, 	
			{ path: 'project/history/:project_uuid',						component: () => import('src/pages/projects/ProjectHistory.vue') }, 	
			{ path: 'project/date/:budget_proposal_uuid',					component: () => import('src/pages/projects/ProjectDate.vue') }, 	
			
			// lista dos projetos e painel
			{ path: 'project/:project_uuid',								component: () => import('src/pages/projects/ProposalList.vue') }, 	
			{ path: 'project/painel/:project_uuid',							component: () => import('src/pages/projects/ProjectPainel.vue') }, 	
			{ path: 'project/details/:project_uuid/:budget_machine_operator_uuid',		component: () => import('src/pages/projects/ProjectDetails.vue') }, 	
			{ path: 'project/date/details/:budget_proposal_uuid',			component: () => import('src/pages/projects/ProjectDateDetails.vue') }, 	
			{ path: 'project/date/list/:project_uuid',						component: () => import('src/pages/projects/ProjectDateList.vue') }, 	


			{ path: 'manage',							component: () => import('src/pages/user_profile/UserManage.vue') }, 	
			{ path: 'favorite',							component: () => import('src/pages/Meet_references/UserFavorite.vue') }, 	
		],
	},

	// - Lista quais empresas um funcionário é associado
	// 	_ GET  / ———> Não tem?  

	// - Lista convites para funcionario se associar a uma empresa
	// 	_ GET /v1/employee/invite/    ok

	// - Aceitar convites
	// 	_ PUT "PUT /v1/employee/invite/accept/([\w-]+)  ok

	// - Recusar convites
	// 	_ DELETE /v1/employee/invite/decline/([\w-]+)  ok

	// - Ver a LISTA de projetos associados a ele
	// 	_ GET /v1/budget/machine/logged/      ok

	// - Ver os DETALHES de um projeto associado a ele
	// 	_ GET  / ———> Não tem? ok

	// - Checkin em um projeto 
	// 	_ POST /v1/machine/operator/checkin/

	// - Checkout em um projeto 
	// 	_ POST /v1/machine/operator/checkout/

	{
		path: '/:catchAll(.*)*',
		component: () => import('pages/ErrorNotFound.vue'),
	},
];

export default routes;
