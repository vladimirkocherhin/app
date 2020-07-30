<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Progress;
use AppBundle\Entity\WordProgress;
use DateTimeZone;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\Routing\Annotation\Route;


class ProgressController extends  Controller
{
    const WORD_LIMIT = 5;

    /**
     * @Route("/training/{slug}/submit")
     * @return Response
     */
    public function submit(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $item) {
            $searchResult = $this->getDoctrine()->getRepository('AppBundle:WordProgress')
                ->findOneBy([
                    'username' => $item['username'],
                    'wordLocationID' => $item['location'],
            ]);
            $searchResult->setStatus('updated3');

            if (!$searchResult){
                $progress = new WordProgress();

                $progress
                    ->setStatus($item['status'])
                    ->setUsername('admin')
                    ->setTimeToReview(\DateTime::createFromFormat('Y-m-d H:i:s.u', $item['time']['date'], new DateTimeZone($item['time']['timezone'])))
                    ->setWordLocationID($item['location']);
                $em->persist($progress);
            }
        }

        $em->flush();

        return new Response('It worked');
    }


    /**
     * @Route("/training/{slug}")
     * @param string $slug
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function training(string $slug)
    {
        //todo find all not legitimate words
        $repo = $this->getDoctrine()->getRepository('AppBundle:WordProgress');

        $qb = $repo->createQueryBuilder('p');
            //->where('p.username = :username')
        $query = $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('p.username', '?1'),
                $qb->expr()->gt('p.timeToReview', '?2')
            ))
            ->setParameter(1, $this->getUser()->getUsername())
            ->setParameter(2, new \DateTime('@'.strtotime('now')))
            ->getQuery();

        $indexList = [];
        foreach ($query->getResult() as $result){
            $indexList[] = $result->getWordLocationID();
            dump($result);
        }
        dump($indexList);

        // todo 5 words of a given location
        /** @var $repository Repository */
        $repository = $this->get( 'ezpublish.api.repository' );
        $searchService = $repository->getSearchService();
        $query = new LocationQuery();
        $query->limit = self::WORD_LIMIT;
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ParentLocationId( $slug ),
            new Criterion\ContentTypeIdentifier(['word']),
            new Criterion\LogicalNot(new Criterion\LocationId($indexList))

        ]);
        //dump($query);die();
        $em = $this->getDoctrine()->getManager();
        $results = $searchService->findLocations($query);

        dump($results); die;

        // todo logged in username
        $username = $this->getUser()->getUsername();

        // todo json response
        $wordsJSON = [];
        foreach ($results->searchHits as $result ){
            $location = $result->valueObject->contentInfo->mainLocationId;
            $word = $result->valueObject->content->fields['word']['eng-GB']->text;
            $translation = $result->valueObject->content->fields['translation']['eng-GB']->text;
            $wordsJSON[] = [
                'location' => $location,
                'word' => $word,
                'translation' => $translation
            ];
        }

        $response = new JsonResponse();

        $response->setData($wordsJSON);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        //todo create progress if possible
        try {
            $progress = new Progress();
            $progress->setUsername($username);
            $em->persist($progress);
            $em->flush();
            return new Response('Created progress with username: '.$progress->getUsername());
        }
        catch (\Exception $e){}

        return $response;
    }
}