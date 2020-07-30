<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WordProgress
 *
 * @ORM\Table(name="word_progress")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WordProgressRepository")
 */
class WordProgress
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TimeToReview", type="datetime")
     */
    private $timeToReview;

    /**
     * @var string
     *
     * @ORM\Column(name="Username", type="string", length=255)
     */
    private $username;

    /**
     * @var int
     *
     * @ORM\Column(name="WordLocationID", type="integer")
     */
    private $wordLocationID;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return WordProgress
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set timeToReview.
     *
     * @param \DateTime $timeToReview
     *
     * @return WordProgress
     */
    public function setTimeToReview($timeToReview)
    {
        $this->timeToReview = $timeToReview;

        return $this;
    }

    /**
     * Get timeToReview.
     *
     * @return \DateTime
     */
    public function getTimeToReview()
    {
        return $this->timeToReview;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return WordProgress
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set wordLocationID.
     *
     * @param int $wordLocationID
     *
     * @return WordProgress
     */
    public function setWordLocationID($wordLocationID)
    {
        $this->wordLocationID = $wordLocationID;

        return $this;
    }

    /**
     * Get wordLocationID.
     *
     * @return int
     */
    public function getWordLocationID()
    {
        return $this->wordLocationID;
    }
}
