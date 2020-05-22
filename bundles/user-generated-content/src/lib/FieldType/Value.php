<?php

namespace EzSystems\UserGeneratedContent\FieldType;

use eZ\Publish\Core\FieldType\Value as FieldTypeValue;

class Value extends FieldTypeValue
{
    /** @var string */
    private $contentTypeIdentifier;

    public function __toString(): string
    {
        return '';
    }
}
