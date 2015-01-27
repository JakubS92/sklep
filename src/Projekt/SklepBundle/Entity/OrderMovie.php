<?php

namespace Projekt\SklepBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * OrderMovie
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderMovie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    *  @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Movie", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="orders_movies",
     *      joinColumns={@ORM\JoinColumn(name="movie_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     **/
    private $movies;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderedAt", type="datetime")
     */
    private $orderedAt;


    public function __construct()
    {
        $this->movies = new \Doctrine\Common\Collections\ArrayCollection();
        // Ustawiamy date na aktualną
        $this->orderedAt = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \stdClass $user
     * @return OrderMovie
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \stdClass 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set movies
     *
     * @return OrderMovie
     */
    public function setMovies($movies)
    {
        $this->movies = $movies;

        return $this;
    }

    /**
     * Get movies
     *
     * @return Movies
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * Set orderedAt
     *
     * @param \DateTime $orderedAt
     * @return OrderMovie
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    /**
     * Get orderedAt
     *
     * @return \DateTime 
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

}
