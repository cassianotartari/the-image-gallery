<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use GuzzleHttp\Client;
use Api\GradeLevel;

$app->get('/copy-courses', function() use ($app) {
    
    /* @var $client Client */
    $client = $app['guzzle'];
    
    $coursesJson = $client
            ->get('/courses')
            ->getBody()
            ->getContents();
    
    $courseList = json_decode($coursesJson);
    
    $coursesIds = $courseList['data'];
    
    $destinationCourses[];
    
    while(!empty($courseList['next'])) {
    
        foreach ($coursesIds as $courseId) {
            $course = $client
                ->get('/courses/'.$courseId)
                ->getBody()
                ->getContents();

            $destinationCourse = [
                'title' => $course['name'],
                'grade' => GradeLevel::getGradeLevelNumber($course['grade']),
                'srcid' => $course['id']
            ];

            array_push($destinationCourses, $destinationCourse);
        }
    }
    
    $client->post('/school/:school-id/courses', [
        'body' => [
            'courses' => json_encode($destinationCourses)
        ]
    ]);
});




$app->get('/upload/', function() use ($app) {
    return $app['twig']->render('upload_form.html.twig');
});

$app->get('/', function() use ($app) {
    $result = $app['db']->executeQuery(
            'SELECT * FROM images ORDER BY date_added DESC LIMIT 10'
    );

    return $app['twig']->render('gallery.html.twig', array(
                'images' => $result->fetchAll(PDO::FETCH_ASSOC),
    ));
});

$app->post('/upload/', function(Request $request) use ($app) {
    $db = $app['db'];
    $files = $request->files;

    if (!$files->has('image')) {
        throw new Exception(
        'Image upload failed. Check maximum filesize limits.'
        );
    }

    // retrieve the image object and check for various errors
    // such as:
    // - file size limit exceeded
    // - file could not be written on disk
    // - file was only partially uploaded
    $image = $files->get('image');
    if ($image->getError()) {
        throw new RuntimeException($image->getErrorMessage());
    }

    // retrieve image info (such as dimensions)
    // getimagesize() returns false if the image is invalid
    $info = getimagesize($image->getPathname());
    if (!$info) {
        throw new Exception('Bad image file');
    }
    // add the uploaded image to the images table, preserving
    // the original name and educated guess at the MIME type
    $db->executeQuery(
            "INSERT INTO
        images (original_name, height, width, type, date_added)
        VALUES (:name, :height, :width, :type, datetime('now'))", [
        'name' => $image->getClientOriginalName(),
        'height' => $info[1],
        'width' => $info[0],
        'type' => $image->getMimeType()
            ]
    );

    // get the image's new database ID
    $id = $db->lastInsertId();

    // move the file to the storage location, using the ID as the filename
    $image->move($app['upload_folder'], 'img_' . $id . '.jpg');

    // Redirect the user to the gallery page
    return new RedirectResponse('/', 302);
});

$app->get('/img/{id}_{size}.jpg', function($id, $size) use ($app) {
    $thumbnailer = $app['thumbnailer'];
    // Fetch the image ID from the database
    $result = $app['db']->executeQuery(
            'SELECT id FROM images WHERE images.id = :image', array('image' => $id)
    );
    $image = $result->fetch(PDO::FETCH_ASSOC);

    // Abort the request if the image was not found in the database
    if (!$image) {
        return $app->abort(404);
    }
    return new BinaryFileResponse(
            $thumbnailer->createFromId($image['id'], $size), 200, array('Content-Type' => 'image/jpeg')
    );
})->value('size', TheImageGallery\Thumbnailer::SMALL);

if ($app['env'] === 'test') {
    return $app;
}

$app->run();
