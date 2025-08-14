import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
	{
		path: '/',
		component: () => import('layouts/MenuNotLogged.vue'),
		children: [
			{ path: '', component: () => import('pages/onboard/SplashScreen.vue') },
			{ path: 'login', component: () => import('pages/onboard/login/LoginView.vue') },
      { path: 'menu', component: () => import('pages/onboard/login/loginMenuDesktop.vue') },
			{ path: 'register', component: () => import('pages/onboard/user/UserCreate.vue') },
			{ path: 'logout', component: () => import('pages/onboard/Logout.vue') },
			{ path: 'password/email', component: () => import('pages/onboard/password/ValidateEmail.vue') },
			{ path: 'password/code', component: () => import('pages/onboard/password/ValidateToken.vue') },
			{ path: 'password/update', component: () => import('pages/onboard/password/UpdatePassword.vue') },
			{ path: 'terms', component: () => import('src/pages/Meet_references/UserTerms.vue') },
			{ path: 'policy', component: () => import('src/pages/Meet_references/UserPolicy.vue') },
			{ path: 'lp', component: () => import('pages/onboard/LP.vue') },
			{ path: 'gps', component: () => import('pages/GPS.vue') },
		],
	},
	{
		path: '/dashboard',
		component: () => import('layouts/DashboardView.vue'),
		children: [
			{ path: ':company_uuid', component: () => import('components/DashboardComponents/TableProjects.vue') },
			{ path: 'project/details/:budget_uuid', component: () => import('components/DashboardComponents/ProjectDetailsDash.vue') },
      // tela para aceitar o próprio projeto
      { path: 'project/details/date/:budget_uuid', component: () => import('components/DashboardComponents/ProjectDateDetailsDash.vue') },

			{ path: 'project/details/create/:budget_uuid', component: () => import('components/DashboardComponents/ProjectDetailsDashCreate.vue') },
			{ path: 'history/:company_uuid', component: () => import('components/DashboardComponents/HistoryDashboard.vue') },
			//opçoes do dash
			{ path: 'tax/:company_uuid', component: () => import('components/DashboardComponents/TaxaInsert.vue') },
      { path: 'overtime/:company_uuid', component: () => import('components/DashboardComponents/OvertimeTaxs.vue') },

      // colaboradores
			{ path: 'employee/:company_uuid', component: () => import('components/DashboardComponents/EmployeeDashboard.vue') },
			{ path: 'employee/operator/:company_uuid', component: () => import('components/DashboardComponents/OperatorDasboard.vue') },
			{ path: 'company/collaborators/:company_uuid/create', component: () => import('components/DashboardComponents/CompanCollaboratorsCreateDashboard.vue') },
      { path: 'role/:company_uuid', component: () => import('pages/company/CompanyRole.vue') },
			//financeiro
			{ path: 'finance/list/:company_uuid', component: () => import('components/DashboardComponents/FinanceListDashboard.vue') },
			{ path: 'finance/:company_uuid/:project_uuid', component: () => import('components/DashboardComponents/FinanceDashboard.vue') },
			//clientes
			{ path: 'customers/:company_uuid', component: () => import('components/DashboardComponents/CustomersDashboard.vue') },
			{ path: 'create/customers/:company_uuid', component: () => import('components/DashboardComponents/CreateCustomersDashboard.vue') },

			// adcionando uma maquina
			{ path: 'list/machine/:company_uuid', component: () => import('components/DashboardComponents/machines/MachinelistDashboard.vue') },
			{ path: 'insert/machine/:company_uuid', component: () => import('components/DashboardComponents/machines/MachineInsertDashboard.vue') },
			{ path: 'edit/machine/:machine_uuid', component: () => import('components/DashboardComponents/machines/MachineEditDashboard.vue') },

			//criando projeto
			{ path: 'create/project/:company_uuid', component: () => import('components/DashboardComponents/CreateProjectDashboard.vue') },
			{ path: 'create/lifting/project/:project_category_uuid', component: () => import('components/DashboardComponents/CreateLiftingDashboard.vue') },
			{ path: 'create/yellow/project/:project_category_uuid', component: () => import('components/DashboardComponents/CreateYellowDashboard.vue') },
			{ path: 'select/machine/project/:project_uuid', component: () => import('components/DashboardComponents/SelectMachineProjectDashboard.vue') },

			//anexando colaborador a uma máquina
			{ path: '/dashboard/role/collaborator/create/:budget_uuid', component: () => import('components/DashboardComponents/CollaboratorMachineInsert.vue') },

			// Chat
			{ path: 'chat', component: () => import('pages/Chat.vue') },
	
		],
	},
	{
		path: '/user',
		component: () => import('layouts/UserTabs.vue'),
		children: [
			// Lista de Chats do Cliente
			{ path: 'chats', component: () => import('src/pages/ClientChatList.vue') },
			
			// Chat do Cliente
			{ path: 'chat/:budget_uuid', component: () => import('src/pages/ClientChat.vue') },
			
			// Usuários
			{ path: 'profile', component: () => import('src/pages/user_profile/UserProfile.vue') },
			{ path: 'service', component: () => import('pages/onboard/ServiceNav.vue') },
			{ path: 'change', component: () => import('pages/onboard/ChangePerfil.vue') },
			{ path: 'manage', component: () => import('src/pages/user_profile/UserManage.vue') },
			{ path: 'manage/invite', component: () => import('src/pages/user_profile/UserInvite.vue') },
			{ path: 'manage/info', component: () => import('src/pages/user_profile/UserManageInfo.vue') },
			{ path: 'manage/info/addaddress', component: () => import('src/pages/user_profile/UserInfoAddAddress.vue') },
			{ path: 'manage/edit/data', component: () => import('src/pages/user_profile/UserEditData.vue') },
			{ path: 'manage/edit/address/:user_address_uuid', component: () => import('src/pages/user_profile/UserEditAddress.vue') },
			// { path: 'manage/edit/otheraddress',			component: () => import('src/pages/user_profile/UserEditOtherAddress.vue') },
			{ path: 'manage/contact', component: () => import('src/pages/user_profile/UserManageContact.vue') },
			{ path: 'manage/edit/phone', component: () => import('src/pages/user_profile/UserEditContactPhone.vue') },
			{ path: 'manage/edit/emergency', component: () => import('src/pages/user_profile/UserEditContactEmergency.vue') },
			{ path: 'manage/edit/addemergency', component: () => import('src/pages/user_profile/UserEditContactEmergency.vue') },
			{ path: 'manage/config/', component: () => import('src/pages/user_profile/UserConfig.vue') },
			{ path: 'timeline/list', component: () => import('src/pages/onboard/ServiceProgress.vue') },


			// Perfil Colaborador
			{ path: 'manage/profilechange', component: () => import('src/pages/user_profile/UserManageProfileChange.vue') },
			{ path: 'manage/employee/:employee_uuid', component: () => import('src/pages/change_profile/EmployeeProfile.vue') },
			{ path: 'manage/employee/documents/:employee_uuid', component: () => import('src/pages/change_profile/EmployeeEdit.vue') },
			{ path: 'manage/employee/certificate/:employee_uuid', component: () => import('src/pages/change_profile/EmployeeCertificate.vue') },
			{ path: 'manage/employee/service/:employee_uuid', component: () => import('src/pages/change_profile/EmployeeService.vue') },
			{ path: 'manage/employee/certificate/create/:employee_uuid', component: () => import('src/pages/change_profile/EmployeeCertificateCreate.vue') },

			// criando empresa
			{ path: 'company', component: () => import('pages/company/CompanyList.vue') },
			{ path: 'company/options/:company_uuid', component: () => import('pages/company/CompanyOptions.vue') },
			{ path: 'company/insert', component: () => import('pages/company/CompanyInsert.vue') },
			{ path: 'company/edit/:company_uuid', component: () => import('pages/company/CompanyEdit.vue') },
			{ path: 'company/equipments/:company_uuid', component: () => import('pages/company/CompanyEquipments.vue') },
			{ path: 'company/collaborators/:company_uuid', component: () => import('pages/company/CompanyCollaborators.vue') },
			{ path: 'company/collaborators/:company_uuid/create', component: () => import('pages/company/CompanCollaboratorsCreate.vue') },

			{ path: 'company/projects/:company_uuid', component: () => import('pages/company/CompanyProjects.vue') },
			{ path: 'company/budgets', component: () => import('pages/company/CompanyBudgets.vue') },

			{ path: 'machine/list/:company_uuid', component: () => import('pages/machine/MachineList.vue') },
			{ path: 'machine/insert/:company_uuid', component: () => import('pages/machine/MachineInsert.vue') },
			{ path: 'machine/edit/:machine_uuid', component: () => import('pages/machine/MachineEdit.vue') },

			{ path: 'budget/', component: () => import('src/pages/projects/ServiceBudget.vue') },

			// criando projeto
			{ path: 'budget/yellow/:project_category_uuid', component: () => import('src/pages/projects/ServiceBudgetYellow.vue') },
			{ path: 'budget/other/:project_category_uuid', component: () => import('src/pages/projects/ServiceBudgetOther.vue') },
			{ path: 'budget/lifting/:project_category_uuid', component: () => import('src/pages/projects/ServiceBudgetLifting.vue') },

			// selecionando empresa/máquina
			{ path: 'project/recomend/:project_uuid', component: () => import('src/pages/projects/ProjectRecomend.vue') },

			{ path: 'project/budget/:budget_uuid', component: () => import('src/pages/projects/ProjectBudget.vue') },
			{ path: 'project/history/:project_uuid', component: () => import('src/pages/projects/ProjectHistory.vue') },
			{ path: 'project/date/:budget_uuid', component: () => import('src/pages/projects/ProjectDate.vue') },

			// lista dos projetos e painel
			{ path: 'project/:project_uuid', component: () => import('src/pages/projects/ProposalList.vue') },
			{ path: 'project/painel/:project_uuid', component: () => import('src/pages/projects/ProjectPainel.vue') },
			{ path: 'project/details/:project_uuid', component: () => import('src/pages/projects/ProjectDetails.vue') },
			{ path: 'project/date/details/:budget_uuid', component: () => import('src/pages/projects/ProjectDateDetails.vue') },
			{ path: 'project/date/list/:project_uuid', component: () => import('src/pages/projects/ProjectDateList.vue') },


			{ path: 'manage', component: () => import('src/pages/user_profile/UserManage.vue') },
			{ path: 'favorite', component: () => import('src/pages/Meet_references/UserFavorite.vue') },
		],
	},
	// Com login de adm
	{
		path: '/admin',
		component: () => import('layouts/MenuAdmin.vue'),
		children: [
			// Init
			// { path: 'dash',								component: () => import('pages/admin/dash/DashView.vue') },
			// { path: 'financial',						component: () => import('pages/admin/financial/Financial.vue') },
			// { path: 'chat',								component: () => import('pages/admin/chat/Chat.vue') },
			// { path: 'chat/selected',					component: () => import('pages/admin/chat/ChatSelected.vue') },
			// { path: 'config', 							component: () => import('pages/admin/config/Config.vue') },
			// { path: 'config/edit', 						component: () => import('pages/admin/config/ConfigEdit.vue') },
			// { path: 'user', 							component: () => import('pages/admin/user/UserList.vue') },
			// { path: 'user/create/', 					component: () => import('pages/admin/user/UserInsert.vue') },
			// { path: 'user/edit/:user_uuid',     		component: () => import('pages/admin/user/UserEdit.vue') },
			// { path: 'user/profile/:user_uuid',  		component: () => import('pages/admin/user/UserProfile.vue') },
		],
	},
	{
		path: '/:catchAll(.*)*',
		component: () => import('pages/ErrorNotFound.vue'),
	},
];

export default routes;
