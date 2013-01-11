<?php
/**
 * InstaTagger
 *
 * @package  tagger
 */
require(dirname(__FILE__).'/boot.php');

$app = new \Slim\Slim();

// The Routes
$app->get('/', function() use ($app)
{
    // The Object -- not setting up the access token yet
    $i = instagram();
    $settings = is_setup();

    if (is_array($settings) AND isset($settings['access_token']))
    {
        $i->setAccessToken($settings['access_token']);
        // Get all user likes
        $likes = $i->getUserMedia();

        // Take a look at the API response
        echo '<pre>';
        print_r($likes);
        echo '<pre>';
    }
    else
    {
        $config = config();
        if (! isset($config['client_id']) OR ! isset($config['client_secret']))
            die('Client id/secret not set.');

        // Great, let's let them login
        echo '<h2>InstaTagger</h2>';
        echo "<a href='{$i->getLoginUrl()}'>Login with Instagram</a>";
    }
});

$app->get('/callback', function() use($app)
{
    $redis = redis();
    if ($redis->get('tagger:application-settings') == NULL)
        $data = array();
    else
        $data = json_decode($redis->get('tagger:application-settings'), TRUE);

    $retrieve = instagram()->getOAuthToken($_GET['code']);

    // Save the data and go back home!
    $redis->set('tagger:application-settings', json_encode($retrieve));
    $app->redirect('/?setup');
});

// Off to the races!
$app->run();