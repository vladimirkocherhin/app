<?php


namespace AppBundle\Controller;


use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class PhraseController extends AbstractController
{
    /**
     * @Route("/phrases")
     * @param Request $request
     * @return Response
     */
    public function all(Request $request)
    {
        $results = $this->getLocations(254, 'phrase');

        $language = explode('/', $request->getPathInfo())[1];

        $i = 0;
        $categories = [];
        $categoryLocationIds = [];
        foreach ($results->searchHits as $result ){
            $categoryLocationIds[] = $result->valueObject->contentInfo->mainLocationId;
            $i++;

            $categories[] = [
                'id' => $result->valueObject->contentInfo->mainLocationId,
                'order' => $i,
                'title' => $this->getFieldByLanguage($result, 'title', $language),

                'polish' => $this->getFieldByLanguage($result, 'title', 'pol-PL'),
                'english' => $this->getFieldByLanguage($result, 'title', 'eng-GB'),
                'ukrainian' => $this->getFieldByLanguage($result, 'title', 'ukr-UA'),
                'turkish' => $this->getFieldByLanguage($result, 'title', 'tur-TR'),
                'german' => $this->getFieldByLanguage($result, 'title', 'ger-DE'),
                'russian' => $this->getFieldByLanguage($result, 'title', 'rus-RU'),
            ];
        }

        $i = 0;
        $phrases = [];
        foreach ($categoryLocationIds as $categoryLocationId){
            $results = $this->getLocations($categoryLocationId, 'phrase');

            $language = explode('/', $request->getPathInfo())[1];

            $i = 0;
            foreach ($results->searchHits as $result ){
                $asd = explode('/', $result->valueObject->content->fields['audio']['pol-PL']->uri);
                $asdf = $result->valueObject->content->fields['audio']['pol-PL'];
                $as = array_key_exists(4, $asd) ? (string)$asd[4] : '';
                $filename = $result->valueObject->content->fields['audio']['pol-PL']->fileName;
                $url = $as !== '' ? $request->getHost().':'.$request->getPort().'/content/download/'. $as.'/audio/'.$filename.'?inLanguage=pol-PL' : '';
                //dump($url);
                $i++;

                $phrases[] = [
                    'id' => $result->valueObject->contentInfo->mainLocationId,
                    'order' => $i,
                    'title' => $this->getFieldByLanguage($result, 'title', $language),
                    'audio' => $url,
                    'category_id' => $categoryLocationId,

                    'polish' => $this->getFieldByLanguage($result, 'title', 'pol-PL'),
                    'english' => $this->getFieldByLanguage($result, 'title', 'eng-GB'),
                    'ukrainian' => $this->getFieldByLanguage($result, 'title', 'ukr-UA'),
                    'turkish' => $this->getFieldByLanguage($result, 'title', 'tur-TR'),
                    'german' => $this->getFieldByLanguage($result, 'title', 'ger-DE'),
                    'russian' => $this->getFieldByLanguage($result, 'title', 'rus-RU'),

                'searchstring' => $this->getFieldByLanguage($result, 'title', 'pol-PL').
                    $this->getFieldByLanguage($result, 'title', 'eng-GB').
                    $this->getFieldByLanguage($result, 'title', 'ukr-UA').
                    $this->getFieldByLanguage($result, 'title', 'tur-TR').
                    $this->getFieldByLanguage($result, 'title', 'ger-DE').
                    $this->getFieldByLanguage($result, 'title', 'rus-RU'),
                ];
            }
        }

        $json = [
            'categories' => $categories,
            'phrases' => $phrases,
        ];

        $response = new JsonResponse();

        $response->setData($json);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        return $response;
    }

    /**
     * @Route("/phrases_tree")
     * @param Request $request
     * @return Response
     */
    public function allTree(Request $request)
    {
        $results = $this->getLocations(254, 'phrase');

        $language = explode('/', $request->getPathInfo())[1];

        $i = 0;
        $categories = [];
        foreach ($results->searchHits as $result ){
            $categoryLocationIds[] = $result->valueObject->contentInfo->mainLocationId;
            $i++;

            $categories[] = [
                'id' => $result->valueObject->contentInfo->mainLocationId,
                'order' => $i,
                'title' => $this->getFieldByLanguage($result, 'title', $language),

                'polish' => $this->getFieldByLanguage($result, 'title', 'pol-PL'),
                'english' => $this->getFieldByLanguage($result, 'title', 'eng-GB'),
                'ukrainian' => $this->getFieldByLanguage($result, 'title', 'ukr-UA'),
                'turkish' => $this->getFieldByLanguage($result, 'title', 'tur-TR'),
                'german' => $this->getFieldByLanguage($result, 'title', 'ger-DE'),
                'russian' => $this->getFieldByLanguage($result, 'title', 'rus-RU'),
                'children' => $this->findChildren($request, $result->valueObject->contentInfo->mainLocationId)
            ];
        }

        $response = new JsonResponse();

        $response->setData($categories);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        return $response;
    }

    private function findChildren(Request $request, $categoryLocationId)
    {
        $results = $this->getLocations($categoryLocationId, 'phrase');

        $language = explode('/', $request->getPathInfo())[1];

        $j = 0;
        $phrases = [];
        foreach ($results->searchHits as $result ){
            $asd = explode('/', $result->valueObject->content->fields['audio']['pol-PL']->uri);
            $asdf = $result->valueObject->content->fields['audio']['pol-PL'];
            $as = array_key_exists(4, $asd) ? (string)$asd[4] : '';
            $filename = $result->valueObject->content->fields['audio']['pol-PL']->fileName;
            $url = $as !== '' ? $request->getHost().':'.$request->getPort().'/content/download/'. $as.'/audio/'.$filename.'?inLanguage=pol-PL' : '';
            $j++;

            $phrases[] = [
                'id' => $result->valueObject->contentInfo->mainLocationId,
                'order' => $j,
                'title' => $this->getFieldByLanguage($result, 'title', $language),
                'audio' => $url,
                'category_id' => $categoryLocationId,

                'polish' => $this->getFieldByLanguage($result, 'title', 'pol-PL'),
                'english' => $this->getFieldByLanguage($result, 'title', 'eng-GB'),
                'ukrainian' => $this->getFieldByLanguage($result, 'title', 'ukr-UA'),
                'turkish' => $this->getFieldByLanguage($result, 'title', 'tur-TR'),
                'german' => $this->getFieldByLanguage($result, 'title', 'ger-DE'),
                'russian' => $this->getFieldByLanguage($result, 'title', 'rus-RU'),

                'searchstring' => $this->getFieldByLanguage($result, 'title', 'pol-PL').
                    $this->getFieldByLanguage($result, 'title', 'eng-GB').
                    $this->getFieldByLanguage($result, 'title', 'ukr-UA').
                    $this->getFieldByLanguage($result, 'title', 'tur-TR').
                    $this->getFieldByLanguage($result, 'title', 'ger-DE').
                    $this->getFieldByLanguage($result, 'title', 'rus-RU'),
            ];
        }

        return $phrases;
    }
}