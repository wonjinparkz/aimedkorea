@php
    $currentLang = session('locale', 'kor');
    
    $translations = [
        'title' => [
            'kor' => '프로필 정보',
            'eng' => 'Profile Information',
            'chn' => '个人信息',
            'hin' => 'प्रोफ़ाइल जानकारी',
            'arb' => 'معلومات الملف الشخصي'
        ],
        'description' => [
            'kor' => '계정의 프로필 정보와 이메일 주소를 업데이트하세요.',
            'eng' => 'Update your account\'s profile information and email address.',
            'chn' => '更新您的账户资料和电子邮件地址。',
            'hin' => 'अपने खाते की प्रोफ़ाइल जानकारी और ईमेल पता अपडेट करें।',
            'arb' => 'قم بتحديث معلومات ملفك الشخصي وعنوان بريدك الإلكتروني.'
        ],
        'photo' => [
            'kor' => '사진',
            'eng' => 'Photo',
            'chn' => '照片',
            'hin' => 'फोटो',
            'arb' => 'صورة'
        ],
        'select_photo' => [
            'kor' => '새 사진 선택',
            'eng' => 'Select A New Photo',
            'chn' => '选择新照片',
            'hin' => 'नई फोटो चुनें',
            'arb' => 'اختر صورة جديدة'
        ],
        'remove_photo' => [
            'kor' => '사진 제거',
            'eng' => 'Remove Photo',
            'chn' => '删除照片',
            'hin' => 'फोटो हटाएं',
            'arb' => 'إزالة الصورة'
        ],
        'name' => [
            'kor' => '이름',
            'eng' => 'Name',
            'chn' => '姓名',
            'hin' => 'नाम',
            'arb' => 'الاسم'
        ],
        'username' => [
            'kor' => '사용자명',
            'eng' => 'Username',
            'chn' => '用户名',
            'hin' => 'उपयोगकर्ता नाम',
            'arb' => 'اسم المستخدم'
        ],
        'email' => [
            'kor' => '이메일',
            'eng' => 'Email',
            'chn' => '电子邮件',
            'hin' => 'ईमेल',
            'arb' => 'البريد الإلكتروني'
        ],
        'email_unverified' => [
            'kor' => '이메일이 확인되지 않았습니다.',
            'eng' => 'Your email address is unverified.',
            'chn' => '您的电子邮件地址未验证。',
            'hin' => 'आपका ईमेल पता सत्यापित नहीं है।',
            'arb' => 'عنوان بريدك الإلكتروني غير موثق.'
        ],
        'resend_verification' => [
            'kor' => '확인 이메일 재전송',
            'eng' => 'Click here to re-send the verification email.',
            'chn' => '点击此处重新发送验证邮件。',
            'hin' => 'सत्यापन ईमेल पुनः भेजने के लिए यहां क्लिक करें।',
            'arb' => 'انقر هنا لإعادة إرسال بريد التحقق.'
        ],
        'verification_sent' => [
            'kor' => '새 확인 링크가 이메일로 전송되었습니다.',
            'eng' => 'A new verification link has been sent to your email address.',
            'chn' => '新的验证链接已发送到您的电子邮件地址。',
            'hin' => 'आपके ईमेल पते पर एक नया सत्यापन लिंक भेजा गया है।',
            'arb' => 'تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.'
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

<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ $translations['title'][$currentLang] ?? $translations['title']['kor'] }}
    </x-slot>

    <x-slot name="description">
        {{ $translations['description'][$currentLang] ?? $translations['description']['kor'] }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $translations['photo'][$currentLang] ?? $translations['photo']['kor'] }}
                </label>

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <button type="button" 
                        x-on:click.prevent="$refs.photo.click()"
                        class="mt-2 me-2 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    {{ $translations['select_photo'][$currentLang] ?? $translations['select_photo']['kor'] }}
                </button>

                @if ($this->user->profile_photo_path)
                    <button type="button" 
                            wire:click="deleteProfilePhoto"
                            class="mt-2 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        {{ $translations['remove_photo'][$currentLang] ?? $translations['remove_photo']['kor'] }}
                    </button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $translations['name'][$currentLang] ?? $translations['name']['kor'] }}
            </label>
            <input id="name" 
                   type="text" 
                   wire:model="state.name" 
                   required 
                   autocomplete="name"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="col-span-6 sm:col-span-4">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $translations['username'][$currentLang] ?? $translations['username']['kor'] }}
            </label>
            <input id="username" 
                   type="text" 
                   wire:model="state.username" 
                   required 
                   autocomplete="username"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            <x-input-error for="username" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $translations['email'][$currentLang] ?? $translations['email']['kor'] }}
            </label>
            <input id="email" 
                   type="email" 
                   wire:model="state.email" 
                   required 
                   autocomplete="email"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-600">
                    {{ $translations['email_unverified'][$currentLang] ?? $translations['email_unverified']['kor'] }}

                    <button type="button" 
                            wire:click.prevent="sendEmailVerification"
                            class="underline text-sm text-blue-600 hover:text-blue-900 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $translations['resend_verification'][$currentLang] ?? $translations['resend_verification']['kor'] }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ $translations['verification_sent'][$currentLang] ?? $translations['verification_sent']['kor'] }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ $translations['saved'][$currentLang] ?? $translations['saved']['kor'] }}
        </x-action-message>

        <button type="submit"
                wire:loading.attr="disabled"
                wire:target="photo"
                class="px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ $translations['save'][$currentLang] ?? $translations['save']['kor'] }}
        </button>
    </x-slot>
</x-form-section>