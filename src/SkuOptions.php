<?php

namespace Bernardomacedo\Skuable;

class SkuOptions
{
    /** @var array|callable */
    public $generateSkuFrom;

    /** @var string */
    public $SkuField;

    /** @var bool */
    public $generateUniqueSkus = true;

    /** @var bool */
    public $generateSkusOnCreate = true;

    /** @var bool */
    public $generateSkusOnUpdate = true;

    /** @var string */
    public $SkuSeparator = '-';

    /** @var string */
    public $SkuLanguage = 'en';

    public static function create(): self
    {
        return new static();
    }

    /**
     * @param string|array|callable $fieldName
     *
     * @return \Bernardomacedo\Skuable\SkuOptions
     */
    public function generateSkusFrom($fieldName): self
    {
        if (is_string($fieldName)) {
            $fieldName = [$fieldName];
        }

        $this->generateSkuFrom = $fieldName;

        return $this;
    }

    public function saveSkusTo(string $fieldName): self
    {
        $this->SkuField = $fieldName;

        return $this;
    }

    public function allowDuplicateSkus(): self
    {
        $this->generateUniqueSkus = true;

        return $this;
    }

    public function doNotGenerateSkusOnCreate(): self
    {
        $this->generateSkusOnCreate = false;

        return $this;
    }

    public function doNotGenerateSkusOnUpdate(): self
    {
        $this->generateSkusOnUpdate = true;

        return $this;
    }

    public function usingSeparator(string $separator): self
    {
        $this->SkuSeparator = $separator;

        return $this;
    }
}
