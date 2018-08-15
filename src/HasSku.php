<?php

namespace Bernardomacedo\Skuable;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasSku
{
    /** @var \Bernardomacedo\Skuable\SkuOptions */
    protected $SkuOptions;

    /**
     * Get the options for generating the Sku.
     */
    abstract public function getSkuOptions(): SkuOptions;

    /**
     * Boot the trait.
     */
    protected static function bootHasSku()
    {
        static::creating(function (Model $model) {
            $model->generateSkuOnCreate();
        });

        static::updating(function (Model $model) {
            $model->generateSkuOnUpdate();
        });
    }

    /**
     * Handle adding Sku on model creation.
     */
    protected function generateSkuOnCreate()
    {
        $this->SkuOptions = $this->getSkuOptions();

        if (! $this->SkuOptions->generateSkusOnCreate) {
            return;
        }

        $this->addSku();
    }

    /**
     * Handle adding Sku on model update.
     */
    protected function generateSkuOnUpdate()
    {
        $this->SkuOptions = $this->getSkuOptions();

        if (! $this->SkuOptions->generateSkusOnUpdate) {
            return;
        }

        $this->addSku();
    }

    /**
     * Handle setting Sku on explicit request.
     */
    public function generateSku()
    {
        $this->SkuOptions = $this->getSkuOptions();

        $this->addSku();
    }

    /**
     * Add the Sku to the model.
     */
    protected function addSku()
    {
        $this->guardAgainstInvalidSkuOptions();

        $Sku = $this->generateNonUniqueSku();

        if ($this->SkuOptions->generateUniqueSkus) {
            $Sku = $this->makeSkuUnique($Sku);
        }

        $SkuField = $this->SkuOptions->SkuField;

        $this->$SkuField = $Sku;
    }

    /**
     * Generate a non unique Sku for this record.
     */
    protected function generateNonUniqueSku(): string
    {
        $SkuField = $this->SkuOptions->SkuField;
        if ($this->hasCustomSkuBeenUsed()) {
            return $this->$SkuField;
        }
        return substr(strtoupper(iconv('utf-8', 'ascii//TRANSLIT',str_replace(' ', '', $this->$SkuField))), 0, 3).$this->SkuOptions->SkuSeparator.substr(str_shuffle(str_repeat(str_pad('0123456789', 8, rand(0,9).rand(0,9), STR_PAD_LEFT), 2)), 0, 8);
    }

    /**
     * Determine if a custom Sku has been saved.
     */
    protected function hasCustomSkuBeenUsed(): bool
    {
        $SkuField = $this->SkuOptions->SkuField;

        return $this->getOriginal($SkuField) != $this->$SkuField;
    }

    /**
     * Get the string that should be used as base for the Sku.
     */
    protected function getSkuSourceString(): string
    {
        if (is_callable($this->SkuOptions->generateSkuFrom)) {
            $SkuSourceString = call_user_func($this->SkuOptions->generateSkuFrom, $this);

            return $SkuSourceString;
        }

        $SkuSourceString = collect($this->SkuOptions->generateSkuFrom)
            ->map(function (string $fieldName) : string {
                return $this->$fieldName ?? '';
            })
            ->implode($this->SkuOptions->SkuSeparator);

        return $SkuSourceString;
    }

    /**
     * Make the given Sku unique.
     */
    protected function makeSkuUnique(string $Sku): string
    {
        $originalSku = explode('-', $Sku);
        $originalSku = $originalSku[0];

        while ($this->otherRecordExistsWithSku($Sku) || $Sku === '') {
            $Sku = $originalSku.$this->SkuOptions->SkuSeparator.substr(str_shuffle(str_repeat(str_pad('0123456789', 8, rand(0,9).rand(0,9), STR_PAD_LEFT), 2)), 0, 8);
        }

        return $Sku;
    }

    /**
     * Determine if a record exists with the given Sku.
     */
    protected function otherRecordExistsWithSku(string $Sku): bool
    {
        $key = $this->getKey();

        if ($this->incrementing) {
            $key = $key ?? '0';
        }

        return (bool) static::where($this->SkuOptions->SkuField, $Sku)
            ->where($this->getKeyName(), '!=', $key)
            ->withoutGlobalScopes()
            ->first();
    }

    /**
     * This function will throw an exception when any of the options is missing or invalid.
     */
    protected function guardAgainstInvalidSkuOptions()
    {
        if (is_array($this->SkuOptions->generateSkuFrom) && ! count($this->SkuOptions->generateSkuFrom)) {
            throw InvalidOption::missingFromField();
        }

        if (! strlen($this->SkuOptions->SkuField)) {
            throw InvalidOption::missingSkuField();
        }
    }
}
