<?php
/**
 * Tagger Shared Functions
 *
 * @package  tagger
 */
if (! defined('ABS')) exit;

/**
 * Retrieve the last photo ID stored
 *
 * @return string 0 if never stored, the photo ID string if has stored
 */
function retrieve_last_id()
{
    return redis()->get('tagger:last-id');
}

/**
 * Set the last photo ID stored
 *
 * @param string
 */
function set_last_id($id)
{
    return redis()->set('tagger:last-id', $id);
}

/**
 * Retrieve the Configuration
 *
 * @return  array
 */
function config()
{
    $config = include(ABS.'config.php');
    return $config;
}

/**
 * Return a Redis Client
 *
 * @return  object
 */
function redis()
{
    $config = config();
    return new Predis\Client(isset($config['redis']) ? $config['redis'] : null);
}

/**
 * Build a URL
 *
 * @return  string
 * @param  string Append to the URL
 */
function url($append = '')
{
    $config = config();

    if (isset($config['base_url']))
        return $config['base_url'].$append;

    if (IS_CLI) :
        $base = 'http://localhost/';
    else :
        // We assume it's placed at the base of the domain
        if ( ! isset( $_SERVER['HTTP_HOST']))
        {
            $parsed = parse_url($_SERVER['REQUEST_URL']);
            $host = str_replace('www.', '', $parsed['host']);     
        }
        else
        {
            $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        }

        $base = 'http://'.$host.'/';
    endif;

    return $base.$append;
}

/**
 * Determine if the application is setup
 *
 * @return  bool|object
 */
function is_setup()
{
    $redis = redis();
    $settings = $redis->get('tagger:application-settings');
    if ($settings !== NULL)
        return json_decode($settings, TRUE);
    else
        return FALSE;
}

/**
 * Retrieve an Instagram object
 *
 * @return  object|bool
 */
function instagram()
{
    $config = config();
    $i = new Instagram(array(
        'apiKey' => $config['client_id'],
        'apiSecret' => $config['client_secret'],
        'apiCallback' => url('callback'),
    ));

    return $i;
}