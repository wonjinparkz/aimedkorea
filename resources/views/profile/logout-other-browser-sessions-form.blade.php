@php
    $currentLang = session('locale', 'kor');
    
    $translations = [
        'title' => [
            'kor' => '브라우저 세션',
            'eng' => 'Browser Sessions',
            'chn' => '浏览器会话',
            'hin' => 'ब्राउज़र सत्र',
            'arb' => 'جلسات المتصفح'
        ],
        'description' => [
            'kor' => '다른 브라우저와 기기에서 활성 세션을 관리하고 로그아웃하세요.',
            'eng' => 'Manage and log out your active sessions on other browsers and devices.',
            'chn' => '管理并退出您在其他浏览器和设备上的活动会话。',
            'hin' => 'अन्य ब्राउज़र और उपकरणों पर अपने सक्रिय सत्रों को प्रबंधित करें और लॉग आउट करें।',
            'arb' => 'إدارة وتسجيل الخروج من جلساتك النشطة على المتصفحات والأجهزة الأخرى.'
        ],
        'info' => [
            'kor' => '필요한 경우 모든 기기의 다른 브라우저 세션에서 로그아웃할 수 있습니다. 최근 세션 중 일부가 아래에 나열되어 있지만 전체 목록이 아닐 수 있습니다. 계정이 도용되었다고 생각되면 비밀번호도 변경해야 합니다.',
            'eng' => 'If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.',
            'chn' => '如有必要，您可以退出所有设备上的所有其他浏览器会话。您的一些最近会话列在下面；但是，此列表可能不完整。如果您认为您的帐户已被盗用，您还应该更新密码。',
            'hin' => 'यदि आवश्यक हो, तो आप अपने सभी उपकरणों पर अपने सभी अन्य ब्राउज़र सत्रों से लॉग आउट कर सकते हैं। आपके कुछ हाल के सत्र नीचे सूचीबद्ध हैं; हालांकि, यह सूची पूर्ण नहीं हो सकती है। यदि आपको लगता है कि आपके खाते से छेड़छाड़ की गई है, तो आपको अपना पासवर्ड भी अपडेट करना चाहिए।',
            'arb' => 'إذا لزم الأمر، يمكنك تسجيل الخروج من جميع جلسات المتصفح الأخرى عبر جميع أجهزتك. بعض جلساتك الحديثة مدرجة أدناه؛ ومع ذلك، قد لا تكون هذه القائمة شاملة. إذا كنت تشعر أن حسابك قد تم اختراقه، فيجب عليك أيضًا تحديث كلمة المرور الخاصة بك.'
        ],
        'unknown' => [
            'kor' => '알 수 없음',
            'eng' => 'Unknown',
            'chn' => '未知',
            'hin' => 'अज्ञात',
            'arb' => 'غير معروف'
        ],
        'this_device' => [
            'kor' => '현재 기기',
            'eng' => 'This device',
            'chn' => '此设备',
            'hin' => 'यह उपकरण',
            'arb' => 'هذا الجهاز'
        ],
        'last_active' => [
            'kor' => '마지막 활동',
            'eng' => 'Last active',
            'chn' => '最后活动',
            'hin' => 'अंतिम सक्रिय',
            'arb' => 'آخر نشاط'
        ],
        'logout_other' => [
            'kor' => '다른 브라우저 세션 로그아웃',
            'eng' => 'Log Out Other Browser Sessions',
            'chn' => '退出其他浏览器会话',
            'hin' => 'अन्य ब्राउज़र सत्र से लॉग आउट करें',
            'arb' => 'تسجيل الخروج من جلسات المتصفح الأخرى'
        ],
        'done' => [
            'kor' => '완료.',
            'eng' => 'Done.',
            'chn' => '完成。',
            'hin' => 'हो गया।',
            'arb' => 'تم.'
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
        <div class="max-w-xl text-sm text-gray-600">
            {{ $translations['info'][$currentLang] ?? $translations['info']['kor'] }}
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-5 space-y-6">
                <!-- Other Browser Sessions -->
                @foreach ($this->sessions as $session)
                    <div class="flex items-center">
                        <div>
                            @if ($session->agent->isDesktop())
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            @endif
                        </div>

                        <div class="ms-3">
                            <div class="text-sm text-gray-600">
                                {{ $session->agent->platform() ? $session->agent->platform() : ($translations['unknown'][$currentLang] ?? $translations['unknown']['kor']) }} - {{ $session->agent->browser() ? $session->agent->browser() : ($translations['unknown'][$currentLang] ?? $translations['unknown']['kor']) }}
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">
                                    {{ $session->ip_address }},

                                    @if ($session->is_current_device)
                                        <span class="text-green-500 font-semibold">{{ $translations['this_device'][$currentLang] ?? $translations['this_device']['kor'] }}</span>
                                    @else
                                        {{ $translations['last_active'][$currentLang] ?? $translations['last_active']['kor'] }} {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center mt-5">
            <button type="button"
                    wire:click="confirmLogout" 
                    wire:loading.attr="disabled"
                    class="px-4 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                {{ $translations['logout_other'][$currentLang] ?? $translations['logout_other']['kor'] }}
            </button>

            <x-action-message class="ms-3" on="loggedOut">
                {{ $translations['done'][$currentLang] ?? $translations['done']['kor'] }}
            </x-action-message>
        </div>

        @php
            $modalTranslations = [
                'confirm_text' => [
                    'kor' => '다른 브라우저 세션에서 로그아웃하려면 비밀번호를 입력하세요.',
                    'eng' => 'Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.',
                    'chn' => '请输入您的密码以确认您要退出所有设备上的其他浏览器会话。',
                    'hin' => 'कृपया अपना पासवर्ड दर्ज करें ताकि आप अपने सभी उपकरणों पर अपने अन्य ब्राउज़र सत्रों से लॉग आउट करना चाहते हैं।',
                    'arb' => 'يرجى إدخال كلمة المرور الخاصة بك لتأكيد رغبتك في تسجيل الخروج من جلسات المتصفح الأخرى عبر جميع أجهزتك.'
                ],
                'password' => [
                    'kor' => '비밀번호',
                    'eng' => 'Password',
                    'chn' => '密码',
                    'hin' => 'पासवर्ड',
                    'arb' => 'كلمة المرور'
                ],
                'cancel' => [
                    'kor' => '취소',
                    'eng' => 'Cancel',
                    'chn' => '取消',
                    'hin' => 'रद्द करें',
                    'arb' => 'إلغاء'
                ]
            ];
        @endphp

        <!-- Log Out Other Devices Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                {{ $translations['logout_other'][$currentLang] ?? $translations['logout_other']['kor'] }}
            </x-slot>

            <x-slot name="content">
                {{ $modalTranslations['confirm_text'][$currentLang] ?? $modalTranslations['confirm_text']['kor'] }}

                <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password" 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="current-password"
                           placeholder="{{ $modalTranslations['password'][$currentLang] ?? $modalTranslations['password']['kor'] }}"
                           x-ref="password"
                           wire:model="password"
                           wire:keydown.enter="logoutOtherBrowserSessions" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button"
                        wire:click="$toggle('confirmingLogout')" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    {{ $modalTranslations['cancel'][$currentLang] ?? $modalTranslations['cancel']['kor'] }}
                </button>

                <button type="button"
                        class="ms-3 px-4 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                    {{ $translations['logout_other'][$currentLang] ?? $translations['logout_other']['kor'] }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
