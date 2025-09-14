@php
    $currentLang = session('locale', 'kor');
    
    $translations = [
        'title' => [
            'kor' => '2단계 인증',
            'eng' => 'Two Factor Authentication',
            'chn' => '双因素认证',
            'hin' => 'दो-कारक प्रमाणीकरण',
            'arb' => 'المصادقة الثنائية'
        ],
        'description' => [
            'kor' => '2단계 인증을 사용하여 계정에 추가 보안을 적용하세요.',
            'eng' => 'Add additional security to your account using two factor authentication.',
            'chn' => '使用双因素认证为您的账户添加额外的安全性。',
            'hin' => 'दो-कारक प्रमाणीकरण का उपयोग करके अपने खाते में अतिरिक्त सुरक्षा जोड़ें।',
            'arb' => 'أضف أمانًا إضافيًا إلى حسابك باستخدام المصادقة الثنائية.'
        ],
        'enabled' => [
            'kor' => '2단계 인증이 활성화되었습니다.',
            'eng' => 'You have enabled two factor authentication.',
            'chn' => '您已启用双因素认证。',
            'hin' => 'आपने दो-कारक प्रमाणीकरण सक्षम किया है।',
            'arb' => 'لقد قمت بتفعيل المصادقة الثنائية.'
        ],
        'not_enabled' => [
            'kor' => '2단계 인증이 활성화되지 않았습니다.',
            'eng' => 'You have not enabled two factor authentication.',
            'chn' => '您尚未启用双因素认证。',
            'hin' => 'आपने दो-कारक प्रमाणीकरण सक्षम नहीं किया है।',
            'arb' => 'لم تقم بتفعيل المصادقة الثنائية.'
        ],
        'finish_enabling' => [
            'kor' => '2단계 인증 활성화를 완료하세요.',
            'eng' => 'Finish enabling two factor authentication.',
            'chn' => '完成启用双因素认证。',
            'hin' => 'दो-कारक प्रमाणीकरण सक्षम करना पूरा करें।',
            'arb' => 'أكمل تفعيل المصادقة الثنائية.'
        ],
        'info' => [
            'kor' => '2단계 인증이 활성화되면 인증 중에 보안 무작위 토큰을 입력하라는 메시지가 표시됩니다. 휴대폰의 Google Authenticator 앱에서 이 토큰을 받을 수 있습니다.',
            'eng' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',
            'chn' => '启用双因素认证后，您将在认证期间被要求输入安全的随机令牌。您可以从手机的Google Authenticator应用程序中获取此令牌。',
            'hin' => 'जब दो-कारक प्रमाणीकरण सक्षम होता है, तो प्रमाणीकरण के दौरान आपसे एक सुरक्षित, यादृच्छिक टोकन के लिए कहा जाएगा। आप इस टोकन को अपने फोन के Google Authenticator एप्लिकेशन से प्राप्त कर सकते हैं।',
            'arb' => 'عند تفعيل المصادقة الثنائية، سيُطلب منك رمز عشوائي آمن أثناء المصادقة. يمكنك الحصول على هذا الرمز من تطبيق Google Authenticator على هاتفك.'
        ],
        'enable' => [
            'kor' => '활성화',
            'eng' => 'Enable',
            'chn' => '启用',
            'hin' => 'सक्षम करें',
            'arb' => 'تفعيل'
        ],
        'disable' => [
            'kor' => '비활성화',
            'eng' => 'Disable',
            'chn' => '禁用',
            'hin' => 'अक्षम करें',
            'arb' => 'تعطيل'
        ],
        'confirm' => [
            'kor' => '확인',
            'eng' => 'Confirm',
            'chn' => '确认',
            'hin' => 'पुष्टि करें',
            'arb' => 'تأكيد'
        ],
        'cancel' => [
            'kor' => '취소',
            'eng' => 'Cancel',
            'chn' => '取消',
            'hin' => 'रद्द करें',
            'arb' => 'إلغاء'
        ],
        'setup_key' => [
            'kor' => '설정 키',
            'eng' => 'Setup Key',
            'chn' => '设置密钥',
            'hin' => 'सेटअप कुंजी',
            'arb' => 'مفتاح الإعداد'
        ],
        'recovery_codes' => [
            'kor' => '복구 코드',
            'eng' => 'Recovery Codes',
            'chn' => '恢复代码',
            'hin' => 'रिकवरी कोड',
            'arb' => 'رموز الاسترداد'
        ],
        'regenerate_codes' => [
            'kor' => '복구 코드 재생성',
            'eng' => 'Regenerate Recovery Codes',
            'chn' => '重新生成恢复代码',
            'hin' => 'रिकवरी कोड पुनः उत्पन्न करें',
            'arb' => 'إعادة إنشاء رموز الاسترداد'
        ],
        'show_codes' => [
            'kor' => '복구 코드 보기',
            'eng' => 'Show Recovery Codes',
            'chn' => '显示恢复代码',
            'hin' => 'रिकवरी कोड दिखाएं',
            'arb' => 'عرض رموز الاسترداد'
        ]
    ];
@endphp

<x-action-section>
    <x-slot name="title">
        {{ $translations['title'][$currentLang] ?? $translations['title']['kor'] }}
    </x-slot>

    <x-slot name="description">
        {{ $translations['description'][$currentLang] ?? $translations['description']['kor'] }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ $translations['finish_enabling'][$currentLang] ?? $translations['finish_enabling']['kor'] }}
                @else
                    {{ $translations['enabled'][$currentLang] ?? $translations['enabled']['kor'] }}
                @endif
            @else
                {{ $translations['not_enabled'][$currentLang] ?? $translations['not_enabled']['kor'] }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                {{ $translations['info'][$currentLang] ?? $translations['info']['kor'] }}
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                        @else
                            {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block bg-white">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        {{ $translations['setup_key'][$currentLang] ?? $translations['setup_key']['kor'] }}: {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <input id="code" type="text" name="code" class="block mt-1 w-1/2 px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />
                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <button type="button" wire:loading.attr="disabled"
                            class="px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $translations['enable'][$currentLang] ?? $translations['enable']['kor'] }}
                    </button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <button type="button" class="me-3 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                            {{ $translations['regenerate_codes'][$currentLang] ?? $translations['regenerate_codes']['kor'] }}
                        </button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <button type="button" wire:loading.attr="disabled"
                                class="me-3 px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ $translations['confirm'][$currentLang] ?? $translations['confirm']['kor'] }}
                        </button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <button type="button" class="me-3 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                            {{ $translations['show_codes'][$currentLang] ?? $translations['show_codes']['kor'] }}
                        </button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <button type="button" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        {{ $translations['cancel'][$currentLang] ?? $translations['cancel']['kor'] }}
                    </button>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" wire:loading.attr="disabled"
                                class="px-4 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ $translations['disable'][$currentLang] ?? $translations['disable']['kor'] }}
                        </button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </x-slot>
</x-action-section>