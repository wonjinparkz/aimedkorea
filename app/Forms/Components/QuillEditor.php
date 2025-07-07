<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Concerns\HasFileAttachments;
use Filament\Forms\Components\Contracts\HasFileAttachments as HasFileAttachmentsContract;

class QuillEditor extends Field implements HasFileAttachmentsContract
{
    use HasFileAttachments;
    
    protected string $view = 'forms.components.quill-editor';
    
    protected array $modules = [
        'toolbar' => [
            [['size' => ['12px', '14px', '16px', '18px', '20px', '24px', '30px', '36px', '48px']]],
            [['header' => [1, 2, 3, 4, 5, 6, false]]],
            ['bold', 'italic', 'underline', 'strike'],
            [['list' => 'ordered'], ['list' => 'bullet']],
            [['color' => []], ['background' => []]],
            [['align' => []]],
            ['link', 'image', 'video']
        ],
        'imageResize' => [
            'parchment' => [
                'image' => [
                    'attributes' => ['width', 'height', 'style']
                ]
            ]
        ]
    ];
    
    protected int $minHeight = 300;
    
    public function modules(array $modules): static
    {
        $this->modules = $modules;
        return $this;
    }
    
    public function minHeight(int $height): static
    {
        $this->minHeight = $height;
        return $this;
    }
    
    public function getModules(): array
    {
        return $this->modules;
    }
    
    public function getMinHeight(): int
    {
        return $this->minHeight;
    }
}