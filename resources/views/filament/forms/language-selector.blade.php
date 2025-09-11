@php
    $record = $getRecord();
    $currentLanguage = $record?->language ?? 'kor';
    $availableLanguages = $record ? $record->getAvailableLanguages() : [];
    // Collection을 배열로 변환
    if ($availableLanguages instanceof \Illuminate\Support\Collection) {
        $availableLanguages = $availableLanguages->toArray();
    }
    $baseSlug = $record?->base_slug ?? null;
@endphp

<div class="space-y-4">
    <!-- Language Badges -->
    <div class="flex flex-wrap gap-2">
        @php
            $languages = [
                ['code' => 'kor', 'label' => '한국어', 'flag' => '🇰🇷'],
                ['code' => 'eng', 'label' => 'English', 'flag' => '🇬🇧'],
                ['code' => 'chn', 'label' => '中文', 'flag' => '🇨🇳'],
                ['code' => 'hin', 'label' => 'हिन्दी', 'flag' => '🇮🇳'],
                ['code' => 'arb', 'label' => 'العربية', 'flag' => '🇸🇦']
            ];
        @endphp
        
        @foreach($languages as $lang)
            @if(is_array($availableLanguages) && in_array($lang['code'], $availableLanguages) && $lang['code'] !== $currentLanguage)
                @php
                    $translation = $record->getTranslation($lang['code']);
                @endphp
                @if($translation)
                    <a
                        href="{{ static::getResource()::getUrl('edit', ['record' => $translation]) }}"
                        @class([
                            'inline-flex items-center px-4 py-2 rounded-lg font-medium transition-all duration-200 cursor-pointer',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ])
                    >
                        <span class="text-lg mr-2">{{ $lang['flag'] }}</span>
                        <span>{{ $lang['label'] }}</span>
                    </a>
                @endif
            @else
                <div
                    @class([
                        'inline-flex items-center px-4 py-2 rounded-lg font-medium transition-all duration-200',
                        'bg-primary-600 text-white' => $currentLanguage === $lang['code'],
                        'bg-gray-50 text-gray-400 border-2 border-dashed border-gray-300' => !is_array($availableLanguages) || !in_array($lang['code'], $availableLanguages),
                    ])
                >
                    <span class="text-lg mr-2">{{ $lang['flag'] }}</span>
                    <span>{{ $lang['label'] }}</span>
                    @if(!is_array($availableLanguages) || !in_array($lang['code'], $availableLanguages))
                        <span class="ml-2 text-xs">(미작성)</span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <!-- Language Status -->
    <div class="text-sm text-gray-600">
        @if($record)
            <p>
                현재 <span class="font-semibold">{{ is_array($availableLanguages) ? count($availableLanguages) : (is_countable($availableLanguages) ? count($availableLanguages) : 0) }}</span>개 언어로 작성됨
            </p>
        @else
            <p class="text-primary-600">
                새 콘텐츠를 생성하면 선택한 언어로 저장됩니다.
            </p>
        @endif
    </div>
</div>