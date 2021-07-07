<?php
// Routes

$app->get('/', App\Action\HomeAction::class)
    ->setName('homepage');

$app->post('/login', App\Action\LoginAction::class)
    ->setName('login');

$app->get('/logout', App\Action\LogoutAction::class)
    ->setName('logout');

$app->get('/register', App\Action\RegisterRouteAction::class)
    ->setName('registerroute');

$app->post('/register', App\Action\RegisterAction::class)
    ->setName('register');

$app->post('/dashboard', App\Action\DashboardAction::class)
    ->setName('dashboard');

$app->get('/dashboard', App\Action\DashboardAction::class)
    ->setName('dashboard');

$app->get('/campers', App\Action\CampersAction::class)
    ->setName('campers');

$app->get('/volunteers', App\Action\VolunteersAction::class)
    ->setName('volunteers');

$app->get('/camper/{id}', App\Action\CamperAction::class)
    ->setName('camper');

$app->get('/volunteer/{id}', App\Action\VolunteerAction::class)
    ->setName('volunteer');

$app->get('/system/manage', App\Action\SystemManageAction::class)
    ->setName('district');

$app->get('/district/{org_id}/manage', App\Action\DistrictManageAction::class)
    ->setName('district');

$app->get('/district/{org_id}/camps', App\Action\CampsShowAction::class)
    ->setName('camps');

$app->get('/camp/{camp_id}/manage', App\Action\CampManageAction::class)
    ->setName('camps');

$app->get('/church/{church_id}/manage', App\Action\ChurchManageAction::class)
    ->setName('church');

$app->get('/applications/{app_id}/edit', App\Action\ApplicationEditRouteAction::class)
    ->setName('applicationedit');

$app->get('/applications/{camp_id}/{app_type}', App\Action\ApplicationRouteAction::class)
    ->setName('church');

$app->post('/appcheck/{camp_id}/{church_id}/{app_type}', App\Action\AppcheckAction::class)
    ->setName('appcheck');

$app->get('/forgot-password', App\Action\PasswordRouteAction::class)
    ->setName('passwordpage');

$app->get('/passwordreset/{pwkey}', App\Action\ResetPasswordRouteAction::class)
    ->setName('resetpasswordrouteaction');

$app->post('/api/applications/{camp_id}/{church_id}/{app_type}', App\Action\ApplicationCreateAction::class)
    ->setName('applicationcreate');

$app->put('/api/applications/{app_id}/update', App\Action\ApplicationUpdateAction::class)
    ->setName('applicationupdate');

$app->delete('/api/applications/{app_id}/delete', App\Action\ApplicationDeleteAction::class)
    ->setName('applicationdelete');

$app->delete('/api/room/{id}', App\Action\RoomDeleteAction::class)
    ->setName('roomdelete');

$app->post('/api/room/{id}/assign', App\Action\RoomAssignAction::class)
    ->setName('roomdelete');

$app->post('/api/room', App\Action\RoomCreateAction::class)
    ->setName('roomdelete');

$app->put('/api/room/{id}', App\Action\RoomUpdateAction::class)
    ->setName('roomdelete');

$app->delete('/api/team/{id}', App\Action\TeamDeleteAction::class)
    ->setName('roomdelete');

$app->post('/api/team/{id}/assign', App\Action\TeamAssignAction::class)
    ->setName('roomdelete');

$app->delete('/api/assignments/{app_id}/{assign_type}/remove', App\Action\AssignmentRemoveAction::class)
    ->setName('assignmentteamremove');

$app->post('/api/team', App\Action\TeamCreateAction::class)
    ->setName('roomdelete');

$app->get('/checkin/{camp_id}', App\Action\CheckinRouteAction::class)
    ->setName('churchcreate');

$app->post('/api/checkin/{camp_id}', App\Action\CheckinRosterAction::class)
    ->setName('churchcreate');

$app->post('/api/reports/baptism/{camp_id}', App\Action\BaptismRosterAction::class)
    ->setName('baptismroster');

$app->delete('/api/leader/{id}', App\Action\LeaderDeleteAction::class)
    ->setName('leaderdelete');

$app->post('/api/churches', App\Action\ChurchCreateAction::class)
    ->setName('churchcreate');

$app->post('/api/invite/{role}', App\Action\InviteRoleAction::class)
    ->setName('inviterole');

$app->post('/api/reservations', App\Action\ReserveCreateAction::class)
    ->setName('reservecreate');

$app->post('/api/camper/register', App\Action\CamperRegisterAction::class)
    ->setName('camperregister');

$app->post('/api/camper/update', App\Action\CamperRegisterAction::class)
    ->setName('camperupdate');

$app->post('/api/volunteer/update', App\Action\VolunteerRegisterAction::class)
    ->setName('volunteerupdate');

$app->post('/api/volunteer/register', App\Action\VolunteerRegisterAction::class)
    ->setName('volunteerregister');

$app->post('/api/camper/payment', App\Action\CamperPaymentAction::class)
    ->setName('camperpayment');

$app->get('/api/reports/camperroster', App\Action\CamperRosterAction::class)
    ->setName('camperroster');

$app->get('/api/reports/volunteerroster', App\Action\VolunteerRosterAction::class)
    ->setName('volunteerroster');

$app->get('/api/reports/camperpayment', App\Action\CamperPaymentReportAction::class)
    ->setName('camperpaymentreport');

$app->post('/api/passwordreset', App\Action\PasswordResetAction::class)
    ->setName('passwordresetaction');

$app->post('/api/resetpassword', App\Action\ResetPasswordAction::class)
    ->setName('resetpasswordaction');

    

