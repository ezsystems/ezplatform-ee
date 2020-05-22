<?php

namespace EzSystems\UserGeneratedContent\FieldType\Converter;

use eZ\Publish\Core\FieldType\FieldSettings;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class UserGeneratedContentConverter implements Converter
{
    public function toStorageValue(
        FieldValue $value,
        StorageFieldValue $storageFieldValue
    ) {
        // TODO: Implement toStorageValue() method.
    }

    public function toFieldValue(
        StorageFieldValue $value,
        FieldValue $fieldValue
    ) {
        // TODO: Implement toFieldValue() method.
    }

    public function toStorageFieldDefinition(
        FieldDefinition $fieldDef,
        StorageFieldDefinition $storageDef
    ) {
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;

        $contentForm = array_values($fieldSettings['content_form']);

        $storageDef->dataText5 = json_encode($contentForm);
    }

    public function toFieldDefinition(
        StorageFieldDefinition $storageDef,
        FieldDefinition $fieldDef
    ) {
        $fieldDef->fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'content_form' => json_decode('{"content_type_identifier": "folder", "fields": [{"enabled": true}]}', true),
                // 'content_form' => json_decode($storageDef->dataText5 ?? '{"content_type_identifier": "folder", "fields": []}', true),
            ]
        );

        $fieldDef->defaultValue->data = [];
    }

    public function getIndexColumn()
    {
        // TODO: Implement getIndexColumn() method.
    }
}
