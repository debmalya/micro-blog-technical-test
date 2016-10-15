<?php



// Silex documentation: http://silex.sensiolabs.org/doc/
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

/* SQLite config

TODO: Add a users table to sqlite db
*/


$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/app.db',
    ),
   
   	/*
    $sql = "CREATE TABLE IF NOT EXISTS users (user_id integer primary key autoincrement, user_name varchar(255) not null)";
	$app['db']->executeQuery($sql);
	 
	$blog_users = array(
		array("id" => 0, "name" => "One"),
		array("id" => 1, "name" => "Two"),
		array("id" => 2, "name" => "Three"),
		array("id" => 3, "name" => "Four"),
	);

	for ($i = 0; $i < count($blog_users); $i++) {
		$app['db']->insert('users', $blog_users[$i]);
	}
	*/
));

// Twig template engine config
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
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

    return $app->json($posts, 200);
});

$app->get('/api/posts/user/{user_id}', function($user_id) use($app) {
    $sql = "SELECT rowid, * FROM posts WHERE user_id = ?";
    $posts = $app['db']->fetchAll($sql, array((int) $user_id));
    if (count($posts) == 0) {
        return $app->json("User id $user_id does not exist.", 404);
    }

    return $app->json($posts, 200);
});

$app->get('/api/posts/id/{post_id}', function($post_id) use($app) {
  $sql = "SELECT rowid, * FROM posts WHERE rowid = ?";
  $post = $app['db']->fetchAssoc($sql, array((int) $post_id));
	
	if (!isset($post[rowid])) {
        return $app->json("Post id $post_id does not exist.", 404);
    }
 
  return $app->json($post, 200);
});

$app->post('/api/posts/new', function (Request $request) {
  //TODO
  $sql = "INSERT INTO posts (content, user_id, date) VALUES (?,?,?)";
//   $user_id = $request->request->get('user_id');
//   $request->attributes->get('slug')
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
    return new Response('I am sorry, but something went terribly wrong.');
});

$app->run();
