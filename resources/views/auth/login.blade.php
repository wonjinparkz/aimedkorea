<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            {{-- Logo slot empty - title moved inside card --}}
        </x-slot>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 text-center">로그인</h2>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Username/Email Input without label --}}
            <div>
                <input id="username" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="text" 
                       name="username" 
                       value="{{ old('username') }}" 
                       placeholder="이메일 또는 사용자명을 입력해주세요" 
                       required 
                       autofocus 
                       autocomplete="username" />
            </div>

            {{-- Password Input without label --}}
            <div class="mt-4">
                <input id="password" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                       type="password" 
                       name="password" 
                       placeholder="비밀번호를 입력해주세요" 
                       required 
                       autocomplete="current-password" />
            </div>

            {{-- Remember Me and Forgot Password on same line --}}
            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                           name="remember">
                    <span class="ms-2 text-sm text-gray-600">로그인 상태 유지</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:text-blue-800 hover:underline" href="{{ route('password.request') }}">
                        비밀번호 찾기
                    </a>
                @endif
            </div>

            {{-- Login Button --}}
            <div class="mt-6">
                <button type="submit" 
                        class="w-full px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    로그인
                </button>
            </div>

            {{-- Register Button --}}
            <div class="mt-3">
                <a href="{{ route('register') }}" 
                   class="block w-full px-4 py-3 bg-white border-2 border-blue-600 rounded-md font-semibold text-center text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    회원가입
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
