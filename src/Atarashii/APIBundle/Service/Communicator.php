<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

class Communicator
{
    private $useragent;
    private $baseurl;
    private $client;
    private $cookies;
    private $response;

    /**
    * Create an instance of the communicator.
    *
    * @param string $baseurl The base URL for the communications. Do not use a terminating slash.
    * @param string $ua      User-Agent to send.
    */
    public function __construct($baseurl, $ua)
    {
        $this->useragent = $ua;
        $this->cookies = new CookiePlugin(new ArrayCookieJar());

        // create http client instance
        $this->client = new Client($baseurl);
        $this->client->addSubscriber($this->cookies);
    }

    /**
    * Login to the MAL Front-end to get a cookie
    *
    * @param string $username MAL Username
    * @param string $password MAL Password
    *
    * @return void
    */
    public function cookieLogin($username, $password)
    {
        // create a request
        $request = $this->client->post("/login.php");
        $request->setHeader('User-Agent', $this->useragent);

        //Add our data transmission - MAL requires the XML content to be in a variable named "data"
        $request->setPostField('username', $username);
        $request->setPostField('password', $password);
        $request->setPostField('sublogin', ' Login ');

        // send request
        $request->send();
    }

    /**
    * Fetch content from a URL
    *
    * @param string $url      Path to access
    * @param string $username Optional MAL Username. Default is null.
    * @param string $password Optional MAL Password. Default is null.
    *
    * @return string Contents of the resource at the supplied path.
    */
    public function fetch($url, $username = null, $password = null)
    {
        // create a request
        $request = $this->client->get($url);
        $request->setHeader('User-Agent', $this->useragent);

        if ($username) {
            $request->setAuth($username, $password);
        }

        // send request / get response
        $this->response = $request->send();

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
    * Send a message on MAL
    *
    * @param string $subject Subject of the message
    * @param string $message body of the message
    *
    * @return void
    */
    public function sendMessage($url, $subject, $message)
    {
        // create a request
        $request = $this->client->post("http://myanimelist.net/mymessages.php?go=send&".$url);
        $request->setHeader('User-Agent', $this->useragent);

        //Add our data transmission - MAL requires the XML content to be in a variable named "data"
        $request->setPostField('subject', $subject);
        $request->setPostField('message', $message);
        $request->setPostField('sendmessage', 'Send Message');

        // send request / get response
        $this->response = $request->send();

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
    * Post content to a URL
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
        // create a request
        $request = $this->client->post($url);
        $request->setHeader('User-Agent', $this->useragent);

        if ($username) {
            $request->setAuth($username, $password);
        }

        //Add our data transmission - MAL requires the XML content to be in a variable named "data"
        $request->setPostField('data', $content);

        // send request / get response
        $this->response = $request->send();

        // this is the response body from the requested page
        return $this->response->getBody();
    }

    /**
    * Determine if a redirect happened
    *
    * @return boolean States if a redirect occurred during the operation
    */
    public function wasRedirected()
    {
        if ($this->response->getRedirectCount()) {
            return true;
        } else {
            return false;
        }
    }
}
