<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallBacks() 
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Atention la date d'arrivé doit etre au bon format")
     * @Assert\GreaterThan("today", message="la date d'arrivé doit être ultérieur à la date d'aujourd'hui !")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Atention la date depart doit etre au bon format")
     * @Assert\GreaterThan(propertyPath="startDate", message="la date de départ doit être plus éloigné à la date d'arrivé !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;



    /**
     * CallBack Appelé a chaque fois qu'on créé une réservation
     *
     * @ORM\Prepersist
     * 
     * @return void
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)){
            //prix de l'annonce * nombre de jours
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }


    public function isBookableDates(){
        // 1) Il faut connaitre les dates impossible pour l'annonce 
        $notAvailableDays = $this->ad->getNotAvailableDays();

        // 2) Il faut comparer les dates choisies avec les dates impossible
        $bookingDays = $this->getDays();


        $format = function($day){
            return $day->format('Y-m-d');
        };

        //Tableau des chaines de caracteres de mes journées
        $days = array_map($format,$bookingDays);

        $notAvailable = array_map($format,$notAvailableDays);

       foreach($days as $day){
           if(array_search($day, $notAvailable) !== false) return false;
       }

       return true;

    }


    /**
     * Permet de récuperer un tableau des journées qui corresponde a ma reservation
     *
     * @return array un tableau datime avec les jour de la reservation
     */
    public function getDays(){
        
        $resultat = range(
            $this->startDate->getTimestamp(), 
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        },$resultat);

        return $days;
    }


    public function getDuration(){
        $diff= $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
