@php
    $currentLang = session('locale', 'kor');
    
    $translations = [
        'title' => [
            'kor' => '비밀번호 변경',
            'eng' => 'Update Password',
            'chn' => '更新密码',
            'hin' => 'पासवर्ड अपडेट करें',
            'arb' => 'تحديث كلمة المرور'
        ],
        'description' => [
            'kor' => '계정 보안을 위해 길고 무작위한 비밀번호를 사용하세요.',
            'eng' => 'Ensure your account is using a long, random password to stay secure.',
            'chn' => '确保您的账户使用长而随机的密码以保持安全。',
            'hin' => 'सुनिश्चित करें कि आपका खाता सुरक्षित रहने के लिए लंबे, यादृच्छिक पासवर्ड का उपयोग कर रहा है।',
            'arb' => 'تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمنًا.'
        ],
        'current_password' => [
            'kor' => '현재 비밀번호',
            'eng' => 'Current Password',
            'chn' => '当前密码',
            'hin' => 'वर्तमान पासवर्ड',
            'arb' => 'كلمة المرور الحالية'
        ],
        'new_password' => [
            'kor' => '새 비밀번호',
            'eng' => 'New Password',
            'chn' => '新密码',
            'hin' => 'नया पासवर्ड',
            'arb' => 'كلمة مرور جديدة'
        ],
        'confirm_password' => [
            'kor' => '비밀번호 확인',
            'eng' => 'Confirm Password',
            'chn' => '确认密码',
            'hin' => 'पासवर्ड की पुष्टि करें',
            'arb' => 'تأكيد كلمة المرور'
        ],
        'saved' => [
            'kor' => '저장되었습니다.',
            'eng' => 'Saved.',
            'chn' => '已保存。',
            'hin' => 'सहेजा गया।',
            'arb' => 'تم الحفظ.'
        ],
        'save' => [
            'kor' => '저장',
            'eng' => 'Save',
            'chn' => '保存',
            'hin' => 'सहेजें',
            'arb' => 'حفظ'
        ]
    ];
@endphp

<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ $translations['title'][$currentLang] ?? $translations['title']['kor'] }}
    </x-slot>

    <x-slot name="description">
        {{ $translations['description'][$currentLang] ?? $translations['description']['kor'] }}
    </x-slot>

    <x-slot name="form">
        <!-- Current Password -->
        <div class="col-span-6 sm:col-span-4">
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $translations['current_password'][$currentLang] ?? $translations['current_password']['kor'] }}
            </label>
            <input id="current_password" 
                   type="password" 
                   wire:model="state.current_password" 
                   autocomplete="current-password"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <!-- New Password -->
        <div class="col-span-6 sm:col-span-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $translations['new_password'][$currentLang] ?? $translations['new_password']['kor'] }}
            </label>
            <input id="password" 
                   type="password" 
                   wire:model="state.password" 
                   autocomplete="new-password"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="col-span-6 sm:col-span-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $translations['confirm_password'][$currentLang] ?? $translations['confirm_password']['kor'] }}
            </label>
            <input id="password_confirmation" 
                   type="password" 
                   wire:model="state.password_confirmation" 
                   autocomplete="new-password"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ $translations['saved'][$currentLang] ?? $translations['saved']['kor'] }}
        </x-action-message>

        <button type="submit"
                class="px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ $translations['save'][$currentLang] ?? $translations['save']['kor'] }}
        </button>
    </x-slot>
</x-form-section>