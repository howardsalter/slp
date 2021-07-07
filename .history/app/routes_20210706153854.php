<?php
// Routes

$app->get('/', App\Action\HomeAction::class)
    ->setName('homepage');

$app->post('/login', App\Action\LoginAction::class)
    ->setName('login');

$app->get('/logout', App\Action\LogoutAction::class)
    ->setName('logout');

$app->post('/dashboard', App\Action\DashboardAction::class)
    ->setName('dashboard');

$app->get('/dashboard', App\Action\DashboardAction::class)
    ->setName('dashboard');
