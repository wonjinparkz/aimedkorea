<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            {{-- Logo slot empty - title moved inside card --}}
        </x-slot>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 text-center">회원가입</h2>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name Input with label --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">이름</label>
                <input id="name" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="이름을 입력해주세요" 
                       required 
                       autofocus 
                       autocomplete="name" />
            </div>

            {{-- Username Input with label --}}
            <div class="mt-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">사용자명</label>
                <input id="username" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="text" 
                       name="username" 
                       value="{{ old('username') }}" 
                       placeholder="사용자명을 입력해주세요" 
                       required 
                       autocomplete="username" />
            </div>

            {{-- Email Input with label --}}
            <div class="mt-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                <input id="email" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="이메일을 입력해주세요" 
                       required 
                       autocomplete="email" />
            </div>

            {{-- Password Input with label --}}
            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">비밀번호</label>
                <input id="password" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="password" 
                       name="password" 
                       placeholder="비밀번호를 입력해주세요" 
                       required 
                       autocomplete="new-password" />
            </div>

            {{-- Password Confirmation Input with label --}}
            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">비밀번호 확인</label>
                <input id="password_confirmation" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="password" 
                       name="password_confirmation" 
                       placeholder="비밀번호를 다시 입력해주세요" 
                       required 
                       autocomplete="new-password" />
            </div>

            {{-- Terms and Privacy Policy Agreement --}}
            <div class="mt-6 border border-gray-200 rounded-md p-4 bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-3">약관 동의</label>
                
                {{-- All Agreement Checkbox --}}
                <div class="mb-3">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               id="agree_all" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                               onchange="toggleAllAgreements(this)">
                        <span class="ms-2 text-sm font-semibold text-gray-900">전체 동의</span>
                    </label>
                </div>
                
                <hr class="my-3 border-gray-200">
                
                {{-- Terms of Service --}}
                <div class="mb-2">
                    <label class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="terms" 
                                   id="terms" 
                                   class="agreement-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                                   required>
                            <span class="ms-2 text-sm text-gray-600">[필수] 이용약관 동의</span>
                        </div>
                        @if (Route::has('terms.show'))
                            <a href="{{ route('terms.show') }}" 
                               target="_blank"
                               class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                약관보기
                            </a>
                        @endif
                    </label>
                </div>
                
                {{-- Privacy Policy --}}
                <div class="mb-2">
                    <label class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="privacy" 
                                   id="privacy" 
                                   class="agreement-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                                   required>
                            <span class="ms-2 text-sm text-gray-600">[필수] 개인정보처리방침 동의</span>
                        </div>
                        @if (Route::has('policy.show'))
                            <a href="{{ route('policy.show') }}" 
                               target="_blank"
                               class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                약관보기
                            </a>
                        @endif
                    </label>
                </div>
                
                {{-- Marketing Agreement (Optional) --}}
                <div class="mb-2">
                    <label class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="marketing" 
                                   id="marketing" 
                                   class="agreement-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ms-2 text-sm text-gray-600">[선택] 마케팅 정보 수신 동의</span>
                        </div>
                        <span class="text-xs text-gray-500">선택</span>
                    </label>
                </div>
            </div>
            
            <script>
                function toggleAllAgreements(checkbox) {
                    const agreements = document.querySelectorAll('.agreement-checkbox');
                    agreements.forEach(function(agreement) {
                        agreement.checked = checkbox.checked;
                    });
                }
                
                // Monitor individual checkboxes to update "agree all" state
                document.addEventListener('DOMContentLoaded', function() {
                    const agreeAll = document.getElementById('agree_all');
                    const agreements = document.querySelectorAll('.agreement-checkbox');
                    
                    agreements.forEach(function(checkbox) {
                        checkbox.addEventListener('change', function() {
                            const allChecked = Array.from(agreements).every(cb => cb.checked);
                            agreeAll.checked = allChecked;
                        });
                    });
                });
            </script>

            {{-- Register Button --}}
            <div class="mt-6">
                <button type="submit" 
                        class="w-full px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    회원가입
                </button>
            </div>

            {{-- Login Link --}}
            <div class="mt-3">
                <a href="{{ route('login') }}" 
                   class="block w-full px-4 py-3 bg-white border-2 border-blue-600 rounded-md font-semibold text-center text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    이미 계정이 있으신가요? 로그인
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
