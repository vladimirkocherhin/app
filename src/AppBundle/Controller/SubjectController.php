<?php


namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;


class SubjectController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function fullViewAction(Request $request, ContentView $view)
    {
        $response = new JsonResponse();

        //dump($view); die;
        $response->setData([
            'name' => $view->getContent()->getName(),
            'author' => $view->getContent()->getFieldValue('author')->text,
        ]);

        return $response;
    }
}