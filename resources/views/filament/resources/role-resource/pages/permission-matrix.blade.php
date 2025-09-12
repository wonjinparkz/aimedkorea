<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 다크모드 대응 스타일 --}}
        <style>
            /* 다크모드 체크박스 커스텀 스타일 */
            .dark input[type="checkbox"]:checked {
                background-color: rgb(59, 130, 246) !important;
                border-color: rgb(59, 130, 246) !important;
            }
            
            .dark input[type="checkbox"]:disabled:checked {
                background-color: rgb(75, 85, 99) !important;
                border-color: rgb(75, 85, 99) !important;
            }
            
            
        </style>
        {{-- 헤더 --}}

        {{-- 권한 매트릭스 --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-900 sticky-header z-10">
                                권한
                            </th>
                            @foreach($this->roles as $role)
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[120px]">
                                    <div class="flex flex-col items-start">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $role->display_name }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Level {{ $role->level }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getPermissionHierarchy() as $sectionKey => $section)
                            {{-- 섹션 헤더 --}}
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white sticky left-0 bg-gray-100 dark:bg-gray-900 sticky-cell z-10">
                                    [{{ $sectionKey }}] {{ $section['name'] }}
                                </td>
                                @foreach($this->roles as $role)
                                    <td class="px-3 py-3 text-left bg-gray-100 dark:bg-gray-900">
                                        @if(isset($section['section_permission']))
                                            @php
                                                $sectionPermission = $this->permissions[$section['section_permission']] ?? null;
                                                $hasSectionPermission = $this->hasPermission($role->id, $section['section_permission']);
                                            @endphp
                                            @if($sectionPermission)
                                                <label class="flex items-center cursor-pointer group gap-1">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:click="togglePermission({{ $role->id }}, '{{ $section['section_permission'] }}')"
                                                        @if($hasSectionPermission) checked @endif
                                                        @if($role->slug === 'admin') disabled @endif
                                                        class="rounded border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-blue-600 dark:text-blue-400 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 
                                                               @if($role->slug === 'admin') opacity-50 cursor-not-allowed @else group-hover:scale-110 transition-transform @endif"
                                                    >
                                                    <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors font-semibold">
                                                        view
                                                    </span>
                                                </label>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            
                            {{-- 하위 권한들 --}}
                            @foreach($section['children'] as $childKey => $child)
                                <tr class="permission-row">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900 sticky-cell z-10 border-r border-gray-200 dark:border-gray-700">
                                        <div class="pl-4">
                                            [{{ $childKey }}] {{ $child['name'] }}
                                        </div>
                                    </td>
                                    @foreach($this->roles as $role)
                                        <td class="px-3 py-4 text-left">
                                            <div class="space-y-1">
                                                @foreach($child['permissions'] as $permissionSlug)
                                                    @php
                                                        $permission = $this->permissions[$permissionSlug] ?? null;
                                                        $hasPermission = $this->hasPermission($role->id, $permissionSlug);
                                                    @endphp
                                                    @if($permission)
                                                        <div class="flex items-center justify-start">
                                                            <label class="flex items-center cursor-pointer group gap-1">
                                                                <input 
                                                                    type="checkbox" 
                                                                    wire:click="togglePermission({{ $role->id }}, '{{ $permissionSlug }}')"
                                                                    @if($hasPermission) checked @endif
                                                                    @if($role->slug === 'admin') disabled @endif
                                                                    class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-blue-600 dark:text-blue-400 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 
                                                                           @if($role->slug === 'admin') opacity-50 cursor-not-allowed @else group-hover:scale-110 transition-transform @endif"
                                                                >
                                                                <span class="text-xs text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                                                    {{ str_replace($permission->module . '-', '', $permissionSlug) }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 도움말 --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">사용 안내</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>관리자</strong> 역할의 권한은 변경할 수 없습니다 (모든 권한을 자동으로 가집니다)</li>
                            <li><strong>view</strong>: 해당 메뉴/기능 보기 권한</li>
                            <li><strong>create</strong>: 새 항목 생성 권한</li>
                            <li><strong>edit</strong>: 기존 항목 수정 권한</li>
                            <li><strong>delete</strong>: 항목 삭제 권한</li>
                            <li><strong>analyze</strong>: 설문 결과 분석 권한</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
