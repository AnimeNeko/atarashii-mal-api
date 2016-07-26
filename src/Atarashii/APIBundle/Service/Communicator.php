<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Service;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception;

class Communicator
{
    private $useragent;
    private $client;
    private $response;

    /**
     * Create an instance of the communicator.
     *
     * @param string $baseUrl   The base URL for the communications. Do not use a terminating slash.
     * @param string $userAgent User-Agent to send.
     */
    public function __construct($baseUrl, $userAgent)
    {
        $this->useragent = $userAgent;

        //Default Options to use in Request
        $requestOptions = Array();
        $requestOptions['base_uri'] = $baseUrl;
        $requestOptions['cookies'] = true;
        $requestOptions['headers']['User-Agent'] = $this->useragent;
        $requestOptions['allow_redirects']['track_redirects'] = true;

        // create http client instance
        $this->client = new Client($requestOptions);
    }

    /**
     * Get the CSRF Token.
     *
     * @return string A string representing the CSRF token required for login
     */
    private function getCsrfToken()
    {
        $token = null;

        //Get the csrf_token for login
        $loginPageContent = $this->fetch('/login.php');

        $crawler = new Crawler();
        $crawler->addHTMLContent($loginPageContent, 'UTF-8');

        $metaTags = $crawler->filter('meta[name="csrf_token"]');

        foreach ($metaTags as $tag) {
            $name = $tag->attributes->getNamedItem('name');

            if ($name !== null && $name->value == 'csrf_token') {
                $token = $tag->attributes->getNamedItem('content')->value;
            }
        }

        return $token;
    }

    /**
     * Login to the MAL Front-end to get a cookie.
     *
     * @param string $username MAL Username
     * @param string $password MAL Password
     *
     * @return bool The confirmation of a login.
     */
    public function cookieLogin($username, $password)
    {
        //Don't bother making a request if the user didn't send any authentication
        if ($username === null || $password === null) {
            return false;
        }

        $token = $this->getCsrfToken();

        // Catch the case where we don't have a token
        if ($token === null) {
            return false;
        }

        //Options for the request
        $requestOptions = Array();

        //Our post form fields are stored in an array named "form_params" in the request options.
        $requestOptions['form_params'] = Array();
        $requestOptions['form_params']['user_name'] = $username;
        $requestOptions['form_params']['password'] = $password;
        $requestOptions['form_params']['submit'] = 'Login';
        $requestOptions['form_params']['csrf_token'] = $token;

        // create a request
        $this->response = $this->client->post('/login.php', $requestOptions);

        if (strpos($this->response->getBody(), 'Invalid password') !== false || strpos($this->response->getBody(), 'Could not find that username') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Fetch content from a URL.
     *
     * @param string $url      Path to access
     * @param string $username Optional MAL Username. Default is null.
     * @param string $password Optional MAL Password. Default is null.
     *
     * @return string Contents of the resource at the supplied path.
     */
    public function fetch($url, $username = null, $password = null)
    {
        $requestOptions = Array();

        if ($username) {
            $requestOptions['auth'] = array($username, $password);
        }

        // create a request
        $this->response = $this->client->get($url, $requestOptions);

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
     * Send a message on MAL.
     *
     * @param string $url     The parameters of the url
     * @param string $subject Subject of the message
     * @param string $message body of the message
     *
     * @return string
     */
    public function sendMessage($url, $subject, $message)
    {
        $requestOptions = Array();

        //Our post form fields are stored in an array named "form_params" in the request options.
        $requestOptions['form_params'] = Array();
        $requestOptions['form_params']['subject'] = $subject;
        $requestOptions['form_params']['message'] = $message;
        $requestOptions['form_params']['csrf_token'] = $this->getCsrfToken();
        $requestOptions['form_params']['sendmessage'] = 'Send Message';


        $this->response = $this->client->post('/mymessages.php?go=send&'.$url, $requestOptions);

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
     * Create a topic on MAL.
     *
     * @param int    $id      The board id
     * @param string $title   Subject of the message
     * @param string $message body of the message
     *
     * @return string
     */
    public function createTopic($id, $title, $message)
    {
        $requestOptions = Array();

        //Our post form fields are stored in an array named "form_params" in the request options.
        $requestOptions['form_params'] = Array();
        $requestOptions['form_params']['topic_title'] = $title;
        $requestOptions['form_params']['msg_text'] = $message;
        $requestOptions['form_params']['csrf_token'] = $this->getCsrfToken();
        $requestOptions['form_params']['submit'] = 'Submit';

        $this->response = $this->client->post('/forum/?action=post&boardid='.$id, $requestOptions);

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
     * Create a comment inside a topic on MAL.
     *
     * @param int    $id      The topic id
     * @param string $message The body of the message
     *
     * @return string
     */
    public function createComment($id, $message)
    {
        $requestOptions = Array();

        //Our post form fields are stored in an array named "form_params" in the request options.
        $requestOptions['form_params'] = Array();
        $requestOptions['form_params']['msg_text'] = $message;
        $requestOptions['form_params']['csrf_token'] = $this->getCsrfToken();
        $requestOptions['form_params']['submit'] = 'Submit';

        $this->response = $this->client->post('/forum/?action=message&topic_id='.$id, $requestOptions);

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
     * edith a comment inside a topic on MAL.
     *
     * @param int    $id      The topic id
     * @param string $message The body of the message
     *
     * @return string
     */
    public function edithComment($id, $message)
    {
        $requestOptions = Array();

        //Our post form fields are stored in an array named "form_params" in the request options.
        $requestOptions['form_params'] = Array();
        $requestOptions['form_params']['msg_text'] = $message;
        $requestOptions['form_params']['csrf_token'] = $this->getCsrfToken();
        $requestOptions['form_params']['submit'] = 'Submit';

        $this->response = $this->client->post('/forum/?action=message&msgid='.$id, $requestOptions);

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
     * Post content to a URL.
     *
     * This function is called sendXML as it's intended to send an XML document to
     * MAL's official API and assumes certain requirements. Note that MAL usually
     * requires authenticated access for API operations, so you should generally
     * supply username and password.
     *
     * @param string $url      Path for posting
     * @param string $content  Content to post to the $url. Generally an XML document.
     * @param string $username Optional MAL Username. Default is null.
     * @param string $password Optional MAL Password. Default is null.
     *
     * @return string Contents of the resource at the supplied path.
     */
    public function sendXML($url, $content, $username = null, $password = null)
    {
        $requestOptions = Array();

        if ($username) {
            $requestOptions['auth'] = array($username, $password);
        }

        //Our post form fields are stored in an array named "form_params" in the request options.
        $requestOptions['form_params'] = Array();

        //Add our data transmission - MAL requires the XML content to be in a variable named "data"
        //If the content is empty, don't send the data.
        if ($content !== '') {
            $requestOptions['form_params']['data'] = $content;
        }

        // Count the times we have tried
        $tryCount = 1;

        do {
            try {
                // send request / get response
                // create a request
                $this->response = $this->client->post($url, $requestOptions);

                // this is the response body from the requested page
                return $this->response->getBody();
            } catch (Exception\ServerException $e) {
                if ($tryCount >= 3) {
                    throw $e;
                }

                ++$tryCount;

                //Sleep for 0.5 seconds (50,000 microseconds)
                usleep(500000);
            }
        } while ($tryCount < 4);
    }

    /**
     * Determine if a redirect happened.
     *
     * @return bool States if a redirect occurred during the operation
     */
    public function wasRedirected()
    {
        $redirects = $this->response->getHeader('X-Guzzle-Redirect-History');

        if (count($redirects)) {
            return true;
        } else {
            return false;
        }
    }
}
