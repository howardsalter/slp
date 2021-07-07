<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass'], array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['mailer'] = function ($container) {
    $mailer = new PHPMailer;

    $mailer->Host = 'mail.fbs.net';  // your email host, to test I use localhost and check emails using test mail server application (catches all  sent mails)
    $mailer->SMTPAuth = true;                 // I set false for localhost
    $mailer->SMTPSecure = 'ssl';              // set blank for localhost
    $mailer->Port = 25;                           // 25 for local host
	$mailer->Username = 'howard.salter@nicad';    // I set sender email in my mailer call
	$mailer->Password = 'nicad07';
	$mailer->isHTML(true);

	return new \App\Mail\Mailer($container->view, $mailer);
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container[App\Action\HomeAction::class] = function ($c) {
    return new App\Action\HomeAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\CampersAction::class] = function ($c) {
    return new App\Action\CampersAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\VolunteersAction::class] = function ($c) {
    return new App\Action\VolunteersAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\SystemManageAction::class] = function ($c) {
    return new App\Action\SystemManageAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\DistrictManageAction::class] = function ($c) {
    return new App\Action\DistrictManageAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\CampsShowAction::class] = function ($c) {
    return new App\Action\CampsShowAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\CampManageAction::class] = function ($c) {
    return new App\Action\CampManageAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ChurchManageAction::class] = function ($c) {
    return new App\Action\ChurchManageAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ApplicationRouteAction::class] = function ($c) {
    return new App\Action\ApplicationRouteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ApplicationEditRouteAction::class] = function ($c) {
    return new App\Action\ApplicationEditRouteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\AppcheckAction::class] = function ($c) {
    return new App\Action\AppcheckAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\RoomCreateAction::class] = function ($c) {
    return new App\Action\RoomCreateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\RoomUpdateAction::class] = function ($c) {
    return new App\Action\RoomUpdateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\RoomDeleteAction::class] = function ($c) {
    return new App\Action\RoomDeleteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\RoomAssignAction::class] = function ($c) {
    return new App\Action\RoomAssignAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\TeamAssignAction::class] = function ($c) {
    return new App\Action\TeamAssignAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\TeamCreateAction::class] = function ($c) {
    return new App\Action\TeamCreateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\TeamDeleteAction::class] = function ($c) {
    return new App\Action\TeamDeleteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\AssignmentRemoveAction::class] = function ($c) {
    return new App\Action\AssignmentRemoveAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\CheckinRouteAction::class] = function ($c) {
    return new App\Action\CheckinRouteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\CheckinRosterAction::class] = function ($c) {
    return new App\Action\CheckinRosterAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\BaptismRosterAction::class] = function ($c) {
    return new App\Action\BaptismRosterAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ApplicationCreateAction::class] = function ($c) {
    return new App\Action\ApplicationCreateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ApplicationUpdateAction::class] = function ($c) {
    return new App\Action\ApplicationUpdateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ApplicationDeleteAction::class] = function ($c) {
    return new App\Action\ApplicationDeleteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ChurchCreateAction::class] = function ($c) {
    return new App\Action\ChurchCreateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ReserveCreateAction::class] = function ($c) {
    return new App\Action\ReserveCreateAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\InviteRoleAction::class] = function ($c) {
    return new App\Action\InviteRoleAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\LeaderDeleteAction::class] = function ($c) {
    return new App\Action\LeaderDeleteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\CamperAction::class] = function ($c) {
    return new App\Action\CamperAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\VolunteerAction::class] = function ($c) {
    return new App\Action\VolunteerAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\RegisterAction::class] = function ($c) {
    return new App\Action\RegisterAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\RegisterRouteAction::class] = function ($c) {
    return new App\Action\RegisterRouteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\CamperRegisterAction::class] = function ($c) {
    $args = $_POST;
    return new App\Action\CamperRegisterAction($c->get('view'), $c->get('logger'), $c->get('db'), $args);
};

$container[App\Action\CamperPaymentAction::class] = function ($c) {
    $args = $_POST;
    return new App\Action\CamperPaymentAction($c->get('view'), $c->get('logger'), $c->get('db'), $args);
};

$container[App\Action\CamperPaymentReportAction::class] = function ($c) {
    $args = $_POST;
    return new App\Action\CamperPaymentReportAction($c->get('view'), $c->get('logger'), $c->get('db'), $args);
};

$container[App\Action\CamperRosterAction::class] = function ($c) {
    $args = $_POST;
    return new App\Action\CamperRosterAction($c->get('view'), $c->get('logger'), $c->get('db'), $args);
};

$container[App\Action\VolunteerRosterAction::class] = function ($c) {
    $args = $_POST;
    return new App\Action\VolunteerRosterAction($c->get('view'), $c->get('logger'), $c->get('db'), $args);
};

$container[App\Action\VolunteerRegisterAction::class] = function ($c) {
    $args = $_POST;
    return new App\Action\VolunteerRegisterAction($c->get('view'), $c->get('logger'), $c->get('db'), $args);
};

$container[App\Action\LoginAction::class] = function ($c) {
    return new App\Action\LoginAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\LogoutAction::class] = function ($c) {
    return new App\Action\LogoutAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\DashboardAction::class] = function ($c) {
    $settings = $c->get('settings');
    $_SESSION['siteUrl'] = $settings['siteUrl'];
    return new App\Action\DashboardAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\InviteAction::class] = function ($c) {
    $settings = $c->get('settings');
    $_SESSION['siteUrl'] = $settings['siteUrl'];
    return new App\Action\InviteAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\PasswordRouteAction::class] = function ($c) {
    return new App\Action\PasswordRouteAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\ResetPasswordRouteAction::class] = function ($c) {
    return new App\Action\ResetPasswordRouteAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\PasswordResetAction::class] = function ($c) {
    return new App\Action\PasswordResetAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ResetPasswordAction::class] = function ($c) {
    return new App\Action\ResetPasswordAction($c->get('view'), $c->get('logger'), $c->get('db'));
};
