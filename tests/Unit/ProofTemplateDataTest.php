<?php

declare(strict_types=1);

use Proovit\LaravelProovit\DTOs\ProofTemplateData;

it('parses a proof template schema and custom fields', function (): void {
    $template = ProofTemplateData::fromArray([
        'id' => 'template-uuid',
        'company_id' => 'company-uuid',
        'name' => 'Delivery note',
        'slug' => 'delivery-note',
        'description' => 'Template description',
        'data_schema' => [
            'signature' => true,
            'shared' => true,
            'displayFolders' => true,
            'displayCategories' => false,
            'displayTags' => true,
            'requiredFiles' => ['photo', 'pdf'],
            'customFields' => [
                [
                    'key' => 'material',
                    'label' => 'Material',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'key' => 'risk_level',
                    'label' => 'Risk level',
                    'type' => 'select',
                    'options' => 'Low,Medium,High',
                    'required' => false,
                ],
            ],
        ],
        'metadata' => [
            'icon' => 'box',
        ],
        'is_active' => true,
        'default' => false,
        'is_importable' => false,
    ]);

    expect($template->id)->toBe('template-uuid')
        ->and($template->companyId)->toBe('company-uuid')
        ->and($template->requiresSignature())->toBeTrue()
        ->and($template->isShared())->toBeTrue()
        ->and($template->displayFolders())->toBeTrue()
        ->and($template->displayCategories())->toBeFalse()
        ->and($template->displayTags())->toBeTrue()
        ->and($template->requiredFiles())->toBe(['photo', 'pdf'])
        ->and($template->customFields())->toHaveCount(2)
        ->and($template->customFields()[1]->optionList())->toBe(['Low', 'Medium', 'High']);
});
