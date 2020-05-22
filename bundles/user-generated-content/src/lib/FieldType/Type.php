<?php

namespace EzSystems\UserGeneratedContent\FieldType;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as FieldTypeValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;

class Type extends FieldType
{
    /**
     * {@inheritdoc}
     */
    protected $settingsSchema = [
        'content_form' => [
            'type' => 'hash',
            'default' => [],
        ],
    ];

    /** @var string */
    private $fieldTypeIdentifier;

    public function __construct(
        string $fieldTypeIdentifier
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
    }

    protected function createValueFromInput($inputValue): Value
    {
        if (is_array($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    public function getName(
        SPIValue $value,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): string {
        return '';
    }

    public function getEmptyValue()
    {
        return new Value();
    }

    public function fromHash($hash): Value
    {
        return $this->getEmptyValue();
    }

    protected function checkValueStructure(FieldTypeValue $value)
    {
        return;
    }

    public function toHash(SPIValue $value): array
    {
        return [];
    }

    /**
     * @return \eZ\Publish\Core\FieldType\ValidationError[]
     */
    public function validateFieldSettings($fieldSettings): array
    {
        return $errors ?? [];
    }
}
