<?php

// Silex documentation: http://silex.sensiolabs.org/doc/
require_once __DIR__ . '/../vendor/autoload.php';
include 'User.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Schema\Table;

$app = new Silex\Application();

$app['debug'] = true;


$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/app.db',
    ),
));

// Twig template engine config
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));


/* ------- micro-blog api ---------

  All CRUD operations performed within our /api/ endpoints below

  TODO: Error checking - e.g. if try retrieve posts for a user_id that does
  not exist, return an error message and an appropriate HTTP status code.

  Implement /api/posts/new endpoint to add a new micro-blog post for a
  given user.

  Extra: Add new API endpoints for any extra features you can think of.

  Extra: Improve on current API code where you see necessary
 */

$app->get('/api/posts', function() use($app) {
    $sql = "SELECT rowid, * FROM posts";
    $posts = $app['db']->fetchAll($sql);
    if (count($posts) == 0) {
        return new Response("There is no post.", 404);
    }
    
    return $app->json($posts, 200);
});

$app->get('/api/formattedposts/{format}', function($format) use($app) {
    $sql = "SELECT rowid, * FROM posts";
    $posts = $app['db']->fetchAll($sql);

    if (count($posts) == 0) {
        return new Response("There is no post.", 404);
    }
    if ($format == 'twig') {
		return $app['twig']->render('blogs.twig', array('posts' => $posts, ));
	}
	return $app->json($post, 200);
});

$app->get('/api/formattedusers/{format}', function($format) use($app) {
    $sql = "SELECT rowid, * FROM users";
    $posts = $app['db']->fetchAll($sql);

    if (count($posts) == 0) {
        //return new Response("Currently no user is configured.", 404);
        return $app['twig']->render('user.link.twig', array('users' => null, ));
    }
    if ($format == 'twig'){
		return $app['twig']->render('user.link.twig', array('users' => $posts, ));
	} 
	return $app->json($post, 200);
});

$app->get('/api/prepareNewPost/{format}', function($format) use($app) {
    $sql = "SELECT rowid, * FROM users";
    $posts = $app['db']->fetchAll($sql);

    if (count($posts) == 0) {
        //return new Response("Currently no user is configured.", 404);
        return $app['twig']->render('create.post.twig', array('users' => null, ));
    }
    if ($format == 'twig'){
		return $app['twig']->render('create.post.twig', array('users' => $posts, ));
	} 
	return $app->json($post, 200);
});

$app->get('/api/posts/user/{user_id}', function($user_id) use($app) {
    $sql = "SELECT rowid, * FROM posts WHERE user_id = ?";
    $posts = $app['db']->fetchAll($sql, array((int) $user_id));
    if (count($posts) == 0) {
        return new Response("User id $user_id does not exist.", 404);
    }
    return $app->json($posts, 200);
})->assert('user_id', '\d+');



$app->get('/api/posts/formatteduser/{user_id}/{format}', function($user_id,$format) use($app) {
    $sql = "SELECT rowid, * FROM posts WHERE user_id = ?";
    $posts = $app['db']->fetchAll($sql, array((int) $user_id));
    // Create user table
    $schema = $app['db']->getSchemaManager();
    if (!$schema->tablesExist('users')) {
    	$users = new Table('users');
    	$users->addColumn('user_id', 'integer', array('unsigned' => false, 'autoincrement' => true));
    	$users->setPrimaryKey(array('user_id'));
    	$users->addColumn('user_name', 'string', array('length' => 32));
    	$users->addUniqueIndex(array('user_name'));
    	$schema->createTable($users);
    	
    	// insert sample rows
    	$app['db']->insert('users', array( 'user_name' => 'User1',));
    	$app['db']->insert('users', array( 'user_name' => 'User2',));
    	$app['db']->insert('users', array( 'user_name' => 'User3',));
    	$app['db']->insert('users', array( 'user_name' => 'User4',));
    }
    if (count($posts) == 0) {
        return $app['twig']->render('blogs.twig', array('posts' => $posts, ));
    }

    if ($format == 'twig'){
    	return $app['twig']->render('blogs.twig', array('posts' => $posts, ));
    }
    return $app->json($post, 200);
})->assert('user_id', '\d+');



$app->get('/api/posts/id/{post_id}', function($post_id) use($app) {
    $sql = "SELECT rowid, * FROM posts WHERE rowid = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $post_id));

    if (!$post) {
        return new Response("Post id $post_id does not exist.", 404);
    }

    return $app->json($post, 200);
    
})->assert('post_id', '\d+');

/**
 * To get existing post in a specific format.
 */
$app->get('/api/formattedposts/id/{post_id}', function($post_id,$format) use($app) {
    $sql = "SELECT rowid, * FROM posts WHERE rowid = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $post_id));

    if (!$post) {
        return $app['twig']->render('blogs.twig', array('posts' => $posts, ));
    }

    if ($format == 'twig') {
    	return $app['twig']->render('blogs.twig', array('posts' => $posts, ));
    } else {
    	return $app->json($post, 200);
    }
})->assert('post_id', '\d+');

/**
 * To create new post.
 * From request parameter get user_id and content.
 * Then insert them into posts table.
 */
$app->post('/api/posts/new', function (Request $request) use ($app) {
	$user_id = $request->request->get('user_id');
	$content = $request->request->get('content');
	
    $app['db']->insert('posts', array( 'content' => $content, 'user_id' =>
    (int)$user_id, 'date' => time()));
//     $posts = array('message' => 'Blog created successfully.');   
    return $app->json($posts, 200);
});


/**
 * To update existing post.
 * From request parameter get post_id and content.
 * Then update them into posts table.
 */
$app->put('/api/posts/update', function (Request $request) use ($app){
    $post_id = $request->request->get('post_id');
    $content = $request->request->get('content');
    $sql = "UPDATE posts SET content = ? WHERE post_id = ?";
    $app['db']->executeUpdate($sql, array('newContent', (String) $content,'existingPostId',$post_id));
}); 

/**
 * To delete existing post.
 * From request parameter get post_id and content.
 * Then update them into posts table.
 */
$app->delete('/api/posts/delete', function (Request $request) use ($app){
    $post_id = $request->request->get('post_id');
    $sql = "DELETE from posts WHERE post_id = ?";
    $app['db']->executeUpdate($sql, array('existingPostId', (int) $post_id));
});



/* ------- micro-blog web app ---------

  All Endpoints for micro-blog web app below.

  TODO: Build front-end of web app in the / endpoint below - Add more
  endpoints if you like.

  See TODO in index.twig for more instructions / suggestions
 */

$app->get('/', function() use($app) {
    return $app['twig']->render('index.twig');
});

$app->error(function (\Exception $e, $code) {
    return new Response('I am sorry, but something went terribly wrong.' . " Error Code :" . $code . " Error message :" . $e->getMessage());
});

Request::enableHttpMethodParameterOverride();
$app->run();
