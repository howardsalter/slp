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
