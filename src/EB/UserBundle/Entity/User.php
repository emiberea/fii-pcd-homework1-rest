<?php

namespace EB\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JmsSerializer;
use EB\CoreBundle\Entity\Shop;

/**
 * User
 *
 * @ORM\Table(name="eb_user")
 * @ORM\Entity(repositoryClass="EB\UserBundle\Repository\UserRepository")
 * @JmsSerializer\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JmsSerializer\Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @JmsSerializer\Expose
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @JmsSerializer\Expose
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @JmsSerializer\Expose
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @JmsSerializer\Expose
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @JmsSerializer\Expose
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="work", type="string", length=255, nullable=true)
     */
    private $work;

    /**
     * @var string
     *
     * @ORM\Column(name="hobbies", type="string", length=255, nullable=true)
     */
    private $hobbies;

    /**
     * @var string
     *
     * @ORM\Column(name="personalDescription", type="string", length=255, nullable=true)
     */
    private $personalDescription;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EB\CoreBundle\Entity\Shop", mappedBy="user")
     */
    private $shops;


    public function __construct()
    {
        parent::__construct();
        $this->shops = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
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
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return string
     */
    public function getFullnameAndUsername()
    {
        return $this->firstname . ' ' . $this->getLastname() . ' (' . $this->getUsername() . ')';
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param \DateTime $birthDate
     * @return $this
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param string $work
     * @return $this
     */
    public function setWork($work)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * @return string
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * @param string $hobbies
     * @return $this
     */
    public function setHobbies($hobbies)
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    /**
     * @return string
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }

    /**
     * @param string $personalDescription
     * @return $this
     */
    public function setPersonalDescription($personalDescription)
    {
        $this->personalDescription = $personalDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalDescription()
    {
        return $this->personalDescription;
    }

    /**
     * @return ArrayCollection
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * @param Shop $shop
     * @return $this
     */
    public function addShop(Shop $shop)
    {
        $this->shops->add($shop);

        return $this;
    }

    /**
     * @param Shop $shop
     * @return $this
     */
    public function removeShop(Shop $shop)
    {
        $this->shops->removeElement($shop);

        return $this;
    }
}
