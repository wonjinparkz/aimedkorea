@php
    $currentLang = session('locale', 'kor');
    
    $translations = [
        'title' => [
            'kor' => '계정 삭제',
            'eng' => 'Delete Account',
            'chn' => '删除账户',
            'hin' => 'खाता हटाएं',
            'arb' => 'حذف الحساب'
        ],
        'description' => [
            'kor' => '계정을 영구적으로 삭제합니다.',
            'eng' => 'Permanently delete your account.',
            'chn' => '永久删除您的账户。',
            'hin' => 'अपना खाता स्थायी रूप से हटाएं।',
            'arb' => 'احذف حسابك نهائيًا.'
        ],
        'warning' => [
            'kor' => '계정이 삭제되면 모든 리소스와 데이터가 영구적으로 삭제됩니다. 계정을 삭제하기 전에 보관하려는 데이터나 정보를 다운로드하세요.',
            'eng' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.',
            'chn' => '一旦您的账户被删除，其所有资源和数据将被永久删除。在删除您的账户之前，请下载您希望保留的任何数据或信息。',
            'hin' => 'एक बार आपका खाता हटाए जाने के बाद, इसके सभी संसाधन और डेटा स्थायी रूप से हटा दिए जाएंगे। अपना खाता हटाने से पहले, कृपया कोई भी डेटा या जानकारी डाउनलोड करें जिसे आप बनाए रखना चाहते हैं।',
            'arb' => 'بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته نهائيًا. قبل حذف حسابك، يرجى تنزيل أي بيانات أو معلومات ترغب في الاحتفاظ بها.'
        ],
        'delete_button' => [
            'kor' => '계정 삭제',
            'eng' => 'Delete Account',
            'chn' => '删除账户',
            'hin' => 'खाता हटाएं',
            'arb' => 'حذف الحساب'
        ],
        'confirm_text' => [
            'kor' => '정말로 계정을 삭제하시겠습니까? 계정이 삭제되면 모든 리소스와 데이터가 영구적으로 삭제됩니다. 계정을 영구적으로 삭제하려면 비밀번호를 입력하세요.',
            'eng' => 'Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
            'chn' => '您确定要删除您的账户吗？一旦您的账户被删除，其所有资源和数据将被永久删除。请输入您的密码以确认您要永久删除您的账户。',
            'hin' => 'क्या आप वाकई अपना खाता हटाना चाहते हैं? एक बार आपका खाता हटाए जाने के बाद, इसके सभी संसाधन और डेटा स्थायी रूप से हटा दिए जाएंगे। कृपया अपना पासवर्ड दर्ज करें ताकि आप अपना खाता स्थायी रूप से हटाना चाहते हैं।',
            'arb' => 'هل أنت متأكد من أنك تريد حذف حسابك؟ بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته نهائيًا. يرجى إدخال كلمة المرور الخاصة بك لتأكيد رغبتك في حذف حسابك نهائيًا.'
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

<x-action-section>
    <x-slot name="title">
        {{ $translations['title'][$currentLang] ?? $translations['title']['kor'] }}
    </x-slot>

    <x-slot name="description">
        {{ $translations['description'][$currentLang] ?? $translations['description']['kor'] }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ $translations['warning'][$currentLang] ?? $translations['warning']['kor'] }}
        </div>

        <div class="mt-5">
            <button type="button"
                    wire:click="confirmUserDeletion" 
                    wire:loading.attr="disabled"
                    class="px-4 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                {{ $translations['delete_button'][$currentLang] ?? $translations['delete_button']['kor'] }}
            </button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                {{ $translations['title'][$currentLang] ?? $translations['title']['kor'] }}
            </x-slot>

            <x-slot name="content">
                {{ $translations['confirm_text'][$currentLang] ?? $translations['confirm_text']['kor'] }}

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password" 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="current-password"
                           placeholder="{{ $translations['password'][$currentLang] ?? $translations['password']['kor'] }}"
                           x-ref="password"
                           wire:model="password"
                           wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button"
                        wire:click="$toggle('confirmingUserDeletion')" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    {{ $translations['cancel'][$currentLang] ?? $translations['cancel']['kor'] }}
                </button>

                <button type="button"
                        class="ms-3 px-4 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        wire:click="deleteUser" 
                        wire:loading.attr="disabled">
                    {{ $translations['delete_button'][$currentLang] ?? $translations['delete_button']['kor'] }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>