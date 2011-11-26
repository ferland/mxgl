<?php

require_once __DIR__ . '/silex/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('/', function () use ($app)
    {
        var_dump($app['facebook']);
        $signedRequest = $app['facebook']->getSignedRequest();
        $userId = $signedRequest['user_id'];
        
        $userGiftsList = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Document\Gift')
            ->findBy(array('userId' => $userId));

        return $app['twig']->render('index.twig', array(
                'facebook' => $app['facebook'],
                'user_id' => $userId,
                'gifts_list' => $userGiftsList,
            ));
    })
    ->method('GET|POST');

$app->post('/add-gift', function(Request $request) use($app)
    {
        $formData = $request->get('gift');
        /* @var $gift Document\Gift */
        $giftDocument = $app['gift'];
        $giftDocument->fromHtmlDom(file_get_html($formData['url']));
        $giftDocument->setUserId($formData['user_id']);

        $app['doctrine.odm.mongodb.dm']->persist($giftDocument);
        $app['doctrine.odm.mongodb.dm']->flush();

        $userGiftsList = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Document\Gift')
            ->findBy(array('user_id' => $formData['user_id']));
        
        return $app['twig']->render('index.twig', array(
                'user_id' => $formData['user_id'],
                'gifts_list' => $userGiftsList,
            ));
    });
$app->run();