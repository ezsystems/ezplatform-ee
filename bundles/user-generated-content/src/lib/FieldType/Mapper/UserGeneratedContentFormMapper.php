<?php

namespace EzSystems\UserGeneratedContent\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\UserGeneratedContent\Form\Type\ContentFormType;
use Symfony\Component\Form\FormInterface;

class UserGeneratedContentFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(
        FormInterface $fieldDefinitionForm,
        FieldDefinitionData $data
    ): void {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;

        $fieldDefinitionForm
            ->add('content_form', ContentFormType::class, [
                'property_path' => 'fieldSettings[content_form]',
                'disabled' => $isTranslation,
            ]);
    }

    public function mapFieldValueForm(
        FormInterface $fieldForm,
        FieldData $data
    ) {
    }
}
