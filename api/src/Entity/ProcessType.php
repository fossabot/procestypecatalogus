<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A process.
 *
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2 *
 *
 * @version    	1.0
 *
 * @link   		http://www.conduction.nl
 *
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *  denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *  collectionOperations={
 *  	"get",
 *  	"post"
 *  },
 * 	itemOperations={
 *     "get"={
 *  		"swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "extend",
 *                          "in" = "query",
 *                          "description" = "Add the properties of the requestType that this process utilizes, requires the request type to be publicly available",
 *                          "required" = false,
 *                          "type" : "boolean"
 *                      }
 *                  }
 *          }
 *  	},
 *     "put",
 *     "delete"
 *  }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProcessTypeRepository")
 */
class ProcessType
{
    /**
     * @var UuidInterface The UUID identifier of this object
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Assert\Uuid
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string The name of this process
     *
     * @example My process
     *
     * @Assert\NotNull
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string The subtitle of this process
     * @example"An example process
     *
     * @Assert\NotNull
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     */
    private $subtitle;

    /**
     * @var string An short description of this process
     *
     * @example This is the best process ever
     *
     * @Assert\Length(
     *      max = 2550
     * )
     * @Groups({"read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string The logo for this process
     *
     * @example https://www.my-organisation.com/logo.png
     *
     * @Assert\Url
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string The RSIN of the organization that owns this process
     *
     * @example 002851234
     *
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 8,
     *      max = 11
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $sourceOrganization;

    /**
     * @var array The stages of this process
     *
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Stage", mappedBy="process", orphanRemoval=true, fetch="EAGER", cascade={"persist"})
     */
    private $stages;

    /**
     * @var string The request that is used for this process
     *
     * @example http://rtc.zaakonline.nl/9bd169ef-bc8c-4422-86ce-a0e7679ab67a
     *
     * @Assert\Url
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     */
    private $request;

    /**
     * @var object The process that this process extends
     *
     * @MaxDepth(1)
     * @Groups({"write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\ProcessType", inversedBy="extendedBy", fetch="EAGER")
     */
    private $extends;

    /**
     * @var object The processs that extend this process
     *
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity="App\Entity\ProcessType", mappedBy="extends")
     */
    private $extendedBy;

    /**
     * @param array|string[] The request properties that are used for this process
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="array", length=255, nullable=true)
     */
    private $properties = [];

    public function __construct()
    {
        $this->stages = new ArrayCollection();
        $this->extendedBy = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSourceOrganization(): ?string
    {
        return $this->sourceOrganization;
    }

    public function setSourceOrganization(string $sourceOrganization): self
    {
        $this->sourceOrganization = $sourceOrganization;

        return $this;
    }

    /**
     * @return Collection|Stage[]
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): self
    {
        if (!$this->stages->contains($stage)) {
            $this->stages[] = $stage;
            $stage->setProcess($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->contains($stage)) {
            $this->stages->removeElement($stage);
            // set the owning side to null (unless already changed)
            if ($stage->setProcess() === $this) {
                $stage->setProcess(null);
            }
        }

        return $this;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getExtends(): ?self
    {
        return $this->extends;
    }

    public function setExtends(?self $extends): self
    {
        $this->extends = $extends;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getExtendedBy(): Collection
    {
        return $this->extendedBy;
    }

    public function addExtendedBy(self $extendedBy): self
    {
        if (!$this->extendedBy->contains($extendedBy)) {
            $this->extendedBy[] = $extendedBy;
            $extendedBy->setExtends($this);
        }

        return $this;
    }

    public function removeExtendedBy(self $extendedBy): self
    {
        if ($this->extendedBy->contains($extendedBy)) {
            $this->extendedBy->removeElement($extendedBy);
            // set the owning side to null (unless already changed)
            if ($extendedBy->getExtends() === $this) {
                $extendedBy->setExtends(null);
            }
        }

        return $this;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function addProperty(string $property): self
    {
        if (!in_array($property, $this->properties)) {
            $this->properties[] = $property;
        }

        return $this;
    }

    public function removeProperty(string $property): self
    {
        if (in_array($property, $this->properties)) {
            unset($this->properties[$property]);
        }

        return $this;
    }
}