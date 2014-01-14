<?php

namespace Atarashii\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return new Response('<html><body><p>API Index</p></body></html>');
    }
}
