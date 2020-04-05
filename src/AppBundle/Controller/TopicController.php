<?php


namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;


class TopicController extends Controller
{
    public function fullViewAction(Request $request, ContentView $view)
    {
        $response = new JsonResponse();

        $response->setData([
            'name' => $view->getContent()->getName(),
        ]);

        return $response;
    }
}