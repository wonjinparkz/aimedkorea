<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Concerns\HasFileAttachments;
use Filament\Forms\Components\Contracts\HasFileAttachments as HasFileAttachmentsContract;

class TinyEditor extends Field implements HasFileAttachmentsContract
{
    use HasFileAttachments;
    
    protected string $view = 'forms.components.tiny-editor';
    
    protected array $toolbarButtons = [
        'undo', 'redo', '|',
        'formatselect', '|',
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|',
        'bullist', 'numlist', 'outdent', 'indent', '|',
        'link', 'image', 'media', '|',
        'removeformat', 'code'
    ];
    
    protected array $plugins = [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
        'preview', 'anchor', 'searchreplace', 'visualblocks', 'code',
        'fullscreen', 'insertdatetime', 'media', 'table', 'help',
        'wordcount', 'autoresize'
    ];
    
    protected int $minHeight = 300;
    protected int $maxHeight = 500;
    
    public function toolbarButtons(array $buttons): static
    {
        $this->toolbarButtons = $buttons;
        return $this;
    }
    
    public function plugins(array $plugins): static
    {
        $this->plugins = $plugins;
        return $this;
    }
    
    public function minHeight(int $height): static
    {
        $this->minHeight = $height;
        return $this;
    }
    
    public function maxHeight(int $height): static
    {
        $this->maxHeight = $height;
        return $this;
    }
    
    public function getToolbarButtons(): array
    {
        return $this->toolbarButtons;
    }
    
    public function getPlugins(): array
    {
        return $this->plugins;
    }
    
    public function getMinHeight(): int
    {
        return $this->minHeight;
    }
    
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }
}