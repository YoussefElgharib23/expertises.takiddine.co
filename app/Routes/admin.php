<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
// make namespace short

use \App\Controllers\settingsController as settings;

// flash
use \App\Middleware\FlashMiddleware as flash;
use \App\Middleware\OldInputMidddleware as old;
    
    
// security , disable direct access
defined('BASEPATH') or exit('No direct script access allowed');




/*
*  Admin Routes
*/
$app->group('', function () use($app,$container) {


    
    $this->get('[/]', 'Pages:home')->setName('admin.index')->add( new App\Middleware\AssistanteMiddleware($container) );
    
    $this->get('/account[/]', 'Home:account')->setName('account')->add( new App\Middleware\AssistanteMiddleware($container) );

        
    // sinitre System
    $this->group('/sinistres', function ( ) {
        $this->get('[/]', 'Cases:index')->setName('sinistres');
        $this->post('/search', 'Cases:search')->setName('sinistres.search');
        $this->get('/create[/]', 'Cases:create')->setName('sinistres.create');
        $this->post('/store[/]', 'Cases:store')->setName('sinistres.store');       
        $this->get('/delete/{id}[/]', 'Cases:delete')->setName('sinistres.delete');
        $this->any('/edit/{id}[/]', 'Cases:edit')->setName('sinistres.edit');
        $this->any('/setPaied/{id}[/]', 'Cases:setPaied')->setName('sinistres.setPaied');
        $this->post('/update/{id}[/]', 'Cases:update')->setName('sinistres.update');
        $this->post('/colaborator/view/{id}[/]', 'Cases:colaborator')->setName('sinistres.colaborator.view');
        $this->get('/bulkdelete[/]', 'Cases:bulkdelete')->setName('sinistres.bulkdelete');
        $this->get('/create/mission/{type}[/]', 'Cases:new_mission')->setName('sinistres.new_mission');
    })->add( new App\Middleware\AssistanteMiddleware($container) );
    

    
    // Notifications System
    $this->group('/notifications', function ( ) {
        $this->get('[/]', 'Notifications:index')->setName('notifications');
        $this->post('/setNoficationsAsSeen[/]', 'Notifications:setAsSeen')->setName('notifications.setAsSeen');
    });
    

    // Payment System
    $this->group('/payments', function ( ) {
        $this->get('[/]', 'Payments:index')->setName('payments');
        $this->get('/user/{id}[/]', 'Payments:user')->setName('payments.user');
    })->add( new App\Middleware\AssistanteMiddleware($container) );  


    // Payment System
    $this->group('/stats', function ( ) {
        $this->get('[/]', 'Stats:index')->setName('stats');
    })->add( new App\Middleware\AssistanteMiddleware($container) );  





    // Missions System 
    $this->group('/missions', function ( ) {
        $this->get('[/]', 'Missions:index')->setName('missions');
        $this->get('/create[/]', 'Missions:create')->setName('missions.create');
        $this->post('/store[/]', 'Missions:store')->setName('missions.store');       
        $this->get('/delete/{id}[/]', 'Missions:delete')->setName('missions.delete');
        $this->any('/edit/{id}[/]', 'Missions:edit')->setName('missions.edit');
        $this->post('/update/{id}[/]', 'Missions:update')->setName('missions.update');
        $this->get('/validate/{id}[/]', 'Missions:validate')->setName('missions.validate');
        $this->get('/reject/{id}[/]', 'Missions:reject')->setName('missions.reject');
        $this->get('/view/{id}[/]', 'Missions:view')->setName('missions.view');
        $this->get('/colaborator[/]', 'Missions:listForcolaborator')->setName('missions.ListForcolaborator');
        $this->any('/upload[/]', 'Missions:upload_gallery')->setName('missions.upload');
        $this->any('/send[/]', 'Missions:send')->setName('missions.send');
        $this->any('/resend[/]', 'Missions:resend')->setName('missions.resend');
        $this->any('/search[/]', 'Missions:search')->setName('colaborator.sinistre.search');
        $this->any('/confirm_uploaded[/]', 'Missions:confirm')->setName('mission.confirm.uploaded');
        $this->any('/download/{id}', 'Missions:download')->setName('mission.download');
        $this->any('/deleteZip/{id}', 'Missions:deleteZip')->setName('mission.deleteZip');
        $this->any('/draft/clean', 'Missions:draftClean')->setName('mission.clean.draft');
    });    
        
    
        
    // users System
    $this->group('/users', function ( ) {
        $this->get('[/]', 'Users:index')->setName('users');
        $this->get('/create[/]', 'Users:create')->setName('users.create');
        $this->post('/store[/]', 'Users:store')->setName('users.store');
        $this->get('/export/csv[/]', 'Users:export_csv')->setName('usersToCsv');
        $this->get('/export/pdf[/]', 'Users:export_pdf')->setName('usersToPdf');        
        $this->get('/delete/{id}[/]', 'Users:delete')->setName('users.delete');
        $this->get('/activate/{id}[/]', 'Users:delete')->setName('users.activate');
        $this->get('/block/{id}[/]', 'Users:block')->setName('users.block');
        $this->get('/bulkdelete[/]', 'Users:bulkdelete')->setName('users.bulkdelete');
        $this->post('/multiaction[/]', 'Users:multiaction')->setName('users.multiaction');
        $this->any('/edit/{id}[/]', 'Users:edit')->setName('users.edit');
        $this->post('/update/{id}[/]', 'Users:update')->setName('users.update');
    })->add( new App\Middleware\AssistanteMiddleware($container) );




})->add( new App\Middleware\AuthMiddleware($container) );


$app->group('/auth', function (){
    $this->post('/login[/]', 'Auth:login')->setName('login');
    $this->get('/logout[/]', 'Auth:logout')->setName('logout');
});


//   Middlewares
$app->add( new flash($container) );
$app->add( new old($container) );