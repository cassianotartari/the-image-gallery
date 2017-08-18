<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

$app->get('/upload/', function() use ($app) {
    return $app['twig']->render('upload_form.html.twig');
});

$app->get('/', function() use ($app) {
    $imageGlob = glob($app['upload_folder'] . '/img*');
    $images = array_map(
        function($val) {
            return basename($val);
        }, $imageGlob
    );

    return $app['twig']->render('gallery.html.twig', array(
        'images' => $images,
    ));
});

$app->post('/upload/', function(Request $request) use ($app) {
    $files = $request->files;

    if ($files->has('image')) {
        $image = $files->get('image');
        $image->move(
            $app['upload_folder'],
            tempnam($app['upload_folder'], 'img_')
        );
    }

    // Redirect the user to the gallery page
    return new RedirectResponse('/', 302);
});

$app->get('/img/{name}/{img}', function($name, $size, Request $request) use ($app) {
    $thumbnailer = $app['thumbnailer'];
    $pathToThumb = $thumbnailer->create($name, $size);

    // abort if we haven't successfully created a thumbnail
    if (empty($pathToThumb) || !file_exists($pathToThumb)) {
        $app->abort(404, 'Image not found');
    }
    return new BinaryFileResponse($pathToThumb);

})->value('size', TheImageGallery\Thumbnailer::SMALL);

$app->run();