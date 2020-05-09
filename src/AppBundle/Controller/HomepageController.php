<?php


namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class HomepageController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function fullViewAction(Request $request)
    {
        $response = new Response();
        return $this->render('/full/home_page.html.twig');
    }
}