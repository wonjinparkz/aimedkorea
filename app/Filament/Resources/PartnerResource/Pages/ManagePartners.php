<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Fieldset;
use Livewire\Attributes\On;
use App\Models\ProjectOption;

class ManagePartners extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PartnerResource::class;

    protected static string $view = 'filament.resources.partner-resource.pages.manage-partners';

    protected static ?string $title = '파트너사 관리';
    
    public ?array $data = [];
    
    public array $partners = [];
    
    public ?string $typeFilter = null;
    
    public ?string $searchQuery = '';
    
    public ?array $editingPartner = null;
    
    public bool $showEditModal = false;
    
    public ?array $bannerSettings = [];
    
    public function mount(): void
    {
        $this->form->fill();
        $this->loadPartners();
        $this->loadBannerSettings();
        
        // Fill banner form separately
        $this->bannerForm->fill([
            'bannerSettings' => $this->bannerSettings
        ]);
    }
    
    protected function loadBannerSettings(): void
    {
        $settings = ProjectOption::get('partners_banner_settings', []);
        
        $this->bannerSettings = [
            'title' => $settings['title'] ?? '글로벌 파트너사',
            'subtitle' => $settings['subtitle'] ?? 'Global Partner',
            'image' => $settings['image'] ?? null,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('새 파트너사 추가')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('파트너사명')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Select::make('type')
                                    ->label('유형')
                                    ->options([
                                        'marketing' => '마케팅 파트너사',
                                        'clinical' => '임상 파트너사',
                                    ])
                                    ->required(),
                            ]),
                            
                        Grid::make(3)
                            ->schema([
                                Select::make('country_code')
                                    ->label('국가')
                                    ->options(get_countries_by_continent())
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $countries = get_countries();
                                            $set('country', $countries[$state] ?? '');
                                            
                                            // Set continent based on country
                                            $continent_key = get_continent_by_country($state);
                                            if ($continent_key) {
                                                $continents = get_continents();
                                                $set('continent', $continents[$continent_key] ?? '');
                                            }
                                        }
                                    }),
                                    
                                TextInput::make('continent')
                                    ->label('대륙')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('국가 선택 시 자동 입력'),
                                    
                                TextInput::make('website')
                                    ->label('홈페이지')
                                    ->url()
                                    ->prefix('https://')
                                    ->placeholder('example.com')
                                    ->helperText('https://는 자동으로 추가됩니다'),
                            ]),
                            
                        Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->maxLength(500),
                    ])
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('파트너사 추가')
                ->icon('heroicon-o-plus')
                ->action(function (): void {
                    $validated = $this->form->getState();
                    
                    // country 필드 설정
                    if (!empty($validated['country_code'])) {
                        $countries = get_countries();
                        $validated['country'] = $countries[$validated['country_code']] ?? '';
                    }
                    
                    // website 필드 처리
                    if (!empty($validated['website'])) {
                        $validated['website'] = 'https://' . ltrim($validated['website'], 'https://');
                    }
                    
                    if (add_partner($validated)) {
                        Notification::make()
                            ->title('파트너사가 추가되었습니다.')
                            ->success()
                            ->send();
                            
                        $this->form->fill();
                        $this->loadPartners();
                    } else {
                        Notification::make()
                            ->title('파트너사 추가에 실패했습니다.')
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('viewMap')
                ->label('지도에서 보기')
                ->icon('heroicon-o-map')
                ->url('/partners')
                ->openUrlInNewTab(),
                
            Action::make('saveBanner')
                ->label('배너 저장')
                ->icon('heroicon-o-photo')
                ->action(function (): void {
                    $this->saveBannerSettings();
                }),
        ];
    }
    
    public function loadPartners(): void
    {
        $partners = collect(get_partners());
        
        // Apply type filter
        if ($this->typeFilter) {
            $partners = $partners->filter(fn ($partner) => $partner['type'] === $this->typeFilter);
        }
        
        // Apply search
        if ($this->searchQuery) {
            $query = strtolower($this->searchQuery);
            $partners = $partners->filter(function ($partner) use ($query) {
                return str_contains(strtolower($partner['name']), $query) ||
                       str_contains(strtolower($partner['country'] ?? ''), $query);
            });
        }
        
        $this->partners = $partners->values()->toArray();
    }
    
    public function updatedTypeFilter(): void
    {
        $this->loadPartners();
    }
    
    public function updatedSearchQuery(): void
    {
        $this->loadPartners();
    }
    
    public function editPartner(string $partnerId): void
    {
        $partner = get_partner($partnerId);
        
        if ($partner) {
            $this->dispatch('open-modal', id: 'edit-partner-modal');
            $this->editingPartner = $partner;
            
            // website 필드 처리
            if (!empty($partner['website'])) {
                $partner['website'] = str_replace('https://', '', $partner['website']);
            }
            
            $this->getEditForm()->fill($partner);
        }
    }
    
    public function updatePartner(): void
    {
        if (!$this->editingPartner) {
            return;
        }
        
        $data = $this->getEditForm()->getState();
        
        // country 필드 설정
        if (!empty($data['country_code'])) {
            $countries = get_countries();
            $data['country'] = $countries[$data['country_code']] ?? '';
        }
        
        // website 필드 처리
        if (!empty($data['website'])) {
            $data['website'] = 'https://' . ltrim($data['website'], 'https://');
        }
        
        if (update_partner($this->editingPartner['id'], $data)) {
            Notification::make()
                ->title('파트너사가 수정되었습니다.')
                ->success()
                ->send();
                
            $this->dispatch('close-modal', id: 'edit-partner-modal');
            $this->editingPartner = null;
            $this->loadPartners();
        } else {
            Notification::make()
                ->title('파트너사 수정에 실패했습니다.')
                ->danger()
                ->send();
        }
    }
    
    public function deletePartner(string $partnerId): void
    {
        if (remove_partner($partnerId)) {
            Notification::make()
                ->title('파트너사가 삭제되었습니다.')
                ->success()
                ->send();
                
            $this->loadPartners();
        } else {
            Notification::make()
                ->title('파트너사 삭제에 실패했습니다.')
                ->danger()
                ->send();
        }
    }
    
    public function getEditFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->label('파트너사명')
                        ->required()
                        ->maxLength(255),
                        
                    Select::make('type')
                        ->label('유형')
                        ->options([
                            'marketing' => '마케팅 파트너사',
                            'clinical' => '임상 파트너사',
                        ])
                        ->disabled()
                        ->required(),
                ]),
                
            Grid::make(3)
                ->schema([
                    Select::make('country_code')
                        ->label('국가')
                        ->options(get_countries_by_continent())
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $countries = get_countries();
                                $set('country', $countries[$state] ?? '');
                                
                                // Set continent based on country
                                $continent_key = get_continent_by_country($state);
                                if ($continent_key) {
                                    $continents = get_continents();
                                    $set('continent', $continents[$continent_key] ?? '');
                                }
                            }
                        }),
                        
                    TextInput::make('continent')
                        ->label('대륙')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('국가 선택 시 자동 입력'),
                        
                    TextInput::make('website')
                        ->label('홈페이지')
                        ->url()
                        ->prefix('https://')
                        ->placeholder('example.com'),
                ]),
                
            Textarea::make('description')
                ->label('설명')
                ->rows(3)
                ->maxLength(500),
        ];
    }
    
    public function getEditForm(): Form
    {
        return $this->makeForm()
            ->schema($this->getEditFormSchema())
            ->statePath('editingPartner');
    }
    
    protected function saveBannerSettings(): void
    {
        $validated = $this->bannerForm->getState();
        $bannerData = $validated['bannerSettings'] ?? [];
        
        ProjectOption::set('partners_banner_settings', $bannerData);
        
        Notification::make()
            ->title('배너 설정이 저장되었습니다.')
            ->success()
            ->send();
            
        $this->loadBannerSettings();
    }
    
    public function bannerForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('배너 설정')
                    ->description('파트너사 페이지 상단 배너를 설정합니다.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('bannerSettings.title')
                                    ->label('메인 문구')
                                    ->placeholder('글로벌 파트너사')
                                    ->required(),
                                    
                                TextInput::make('bannerSettings.subtitle')
                                    ->label('서브 문구')
                                    ->placeholder('Global Partner'),
                            ]),
                            
                        FileUpload::make('bannerSettings.image')
                            ->label('배너 이미지')
                            ->image()
                            ->directory('partners-banner')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120)
                            ->helperText('이미지가 없는 경우 기본 색상 배경이 적용됩니다.'),
                    ])
            ]);
    }
    
    protected function getForms(): array
    {
        return [
            'form',
            'editForm' => $this->getEditForm(),
            'bannerForm' => $this->bannerForm($this->makeForm())
                ->statePath('bannerSettings'),
        ];
    }
}