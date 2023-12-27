<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Entity\Setting;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JsonSerializable;
use LogicException;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_settings_setting")
 */
class Setting implements SettingInterface
{
    use TimestampableTrait;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $vendor;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $plugin;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $path;

    /**
     * @var string|null
     * @ORM\Column(name="locale_code", type="string", length=5, nullable=true)
     */
    private ?string $localeCode;

    /**
     * @var string|null
     * @ORM\Column(name="storage_type", type="string", length=10, nullable=false)
     */
    private ?string $storageType;

    /**
     * @var string|null
     * @ORM\Column(name="text_value", type="text", length=65535, nullable=true)
     */
    private ?string $textValue;

    /**
     * @var bool|null
     * @ORM\Column(name="boolean_value", type="boolean", nullable=true)
     */
    private ?bool $booleanValue;

    /**
     * @var int|null
     * @ORM\Column(name="integer_value", type="integer", nullable=true)
     */
    private ?int $integerValue;

    /**
     * @var float|null
     * @ORM\Column(name="float_value", type="float", nullable=true)
     */
    private ?float $floatValue;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(name="datetime_value", type="datetime", nullable=true)
     */
    private ?DateTimeInterface $datetimeValue;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(name="date_value", type="date", nullable=true)
     */
    private ?DateTimeInterface $dateValue;

    /**
     * @var array|null
     * @ORM\Column(name="json_value", type="json", nullable=true)
     */
    private ?array $jsonValue;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(name="created_at", type="datetime_immutable")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (null === $this->getStorageType()) {
            return null;
        }
        $getter = 'get' . $this->getStorageType() . 'value';

        return $this->{$getter}();
    }

    /**
     * @param $value
     *
     * @return void
     */
    public function setValue($value): void
    {
        if (null === $this->getStorageType()) {
            throw new LogicException('The storage type MUST be defined before setting the value using ' . __METHOD__ . '.');
        }
        $setter = 'set' . $this->getStorageType() . 'value';
        $this->{$setter}($value);
    }

    /**
     * @return string|null
     */
    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    /**
     * @param string|null $vendor
     */
    public function setVendor(?string $vendor): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return string|null
     */
    public function getPlugin(): ?string
    {
        return $this->plugin;
    }

    /**
     * @param string|null $plugin
     */
    public function setPlugin(?string $plugin): void
    {
        $this->plugin = $plugin;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string|null
     */
    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * @param string|null $localeCode
     */
    public function setLocaleCode(?string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    /**
     * @return string|null
     */
    public function getStorageType(): ?string
    {
        return $this->storageType;
    }

    /**
     * @param string|null $storageType
     */
    public function setStorageType(?string $storageType): void
    {
        $this->storageType = $storageType;
    }

    /**
     * @param mixed $value
     *
     * @throws LogicException
     *
     * @return void
     */
    public function setStorageTypeFromValue($value): void
    {
        $this->setStorageType(
            $this->getTypeFromValue($value)
        );
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function getTypeFromValue($value): string
    {
        $types = [
            'double' => function(): string {
                return SettingInterface::STORAGE_TYPE_FLOAT;
            },
            'array' => function(): string {
                return SettingInterface::STORAGE_TYPE_JSON;
            },
            'object' => function(object $value): string {
                if ($value instanceof DateTimeInterface) {
                    return SettingInterface::STORAGE_TYPE_DATETIME;
                }
                if ($value instanceof JsonSerializable) {
                    return SettingInterface::STORAGE_TYPE_JSON;
                }
                throw new LogicException('Impossible to match the type of the value.');
            },
            'string' => function(): string {
                return SettingInterface::STORAGE_TYPE_TEXT;
            },
            'boolean' => function(): string {
                return SettingInterface::STORAGE_TYPE_BOOLEAN;
            },
            'integer' => function(): string {
                return SettingInterface::STORAGE_TYPE_INTEGER;
            },
            'NULL' => function(): string {
                return SettingInterface::STORAGE_TYPE_TEXT;
            },
        ];

        $type = \gettype($value);
        if (!isset($types[$type])) {
            throw new LogicException(sprintf('Impossible to match the type of the value. (%s)', $type));
        }

        return $types[$type]($value);
    }

    /**
     * @return string|null
     */
    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    /**
     * @param string|null $textValue
     */
    public function setTextValue(?string $textValue): void
    {
        $this->textValue = $textValue;
    }

    /**
     * @return bool|null
     */
    public function getBooleanValue(): ?bool
    {
        return $this->booleanValue;
    }

    /**
     * @param bool|null $booleanValue
     */
    public function setBooleanValue(?bool $booleanValue): void
    {
        $this->booleanValue = $booleanValue;
    }

    /**
     * @return int|null
     */
    public function getIntegerValue(): ?int
    {
        return $this->integerValue;
    }

    /**
     * @param int|null $integerValue
     */
    public function setIntegerValue(?int $integerValue): void
    {
        $this->integerValue = $integerValue;
    }

    /**
     * @return float|null
     */
    public function getFloatValue(): ?float
    {
        return $this->floatValue;
    }

    /**
     * @param float|null $floatValue
     */
    public function setFloatValue(?float $floatValue): void
    {
        $this->floatValue = $floatValue;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDatetimeValue(): ?DateTimeInterface
    {
        return $this->datetimeValue;
    }

    /**
     * @param DateTimeInterface|null $datetimeValue
     */
    public function setDatetimeValue(?DateTimeInterface $datetimeValue): void
    {
        $this->datetimeValue = $datetimeValue;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateValue(): ?DateTimeInterface
    {
        return $this->dateValue;
    }

    /**
     * @param DateTimeInterface|null $dateValue
     */
    public function setDateValue(?DateTimeInterface $dateValue): void
    {
        $this->dateValue = $dateValue;
    }

    /**
     * @return array|null
     */
    public function getJsonValue(): ?array
    {
        return $this->jsonValue;
    }

    /**
     * @param array|null $jsonValue
     */
    public function setJsonValue(?array $jsonValue): void
    {
        $this->jsonValue = $jsonValue;
    }
}
