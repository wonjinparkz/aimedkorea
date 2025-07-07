<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->bannerForm }}
    </div>
    
    <div class="mt-8">
        {{ $this->form }}
        
        <div class="flex justify-end gap-3 mt-4">
            @foreach($this->getHeaderActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </div>
    
    <div class="mt-8 space-y-6">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">검색</label>
                        <input type="text" 
                               wire:model.live="searchQuery" 
                               placeholder="파트너사명, 국가로 검색..."
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">유형 필터</label>
                        <select wire:model.live="typeFilter" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">전체</option>
                            <option value="marketing">마케팅 파트너사</option>
                            <option value="clinical">임상 파트너사</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <div class="text-sm text-gray-500">
                            총 {{ count($partners) }}개의 파트너사
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                파트너사명
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                유형
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                대륙
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                국가
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                홈페이지
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                등록일
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                작업
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($partners as $partner)
                            <tr wire:key="partner-{{ $partner['id'] }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $partner['name'] }}
                                    </div>
                                    @if(!empty($partner['description']))
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($partner['description'], 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $partner['type'] === 'marketing' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $partner['type'] === 'marketing' ? '마케팅' : '임상' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $partner['continent'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $partner['country'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(!empty($partner['website']))
                                        <a href="{{ $partner['website'] }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                            방문하기
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($partner['created_at']) ? \Carbon\Carbon::parse($partner['created_at'])->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="editPartner('{{ $partner['id'] }}')" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        수정
                                    </button>
                                    <button wire:click="deletePartner('{{ $partner['id'] }}')" 
                                            wire:confirm="정말로 이 파트너사를 삭제하시겠습니까?"
                                            class="text-red-600 hover:text-red-900">
                                        삭제
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    등록된 파트너사가 없습니다.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <x-filament::modal id="edit-partner-modal" width="2xl">
        <x-slot name="heading">
            파트너사 수정
        </x-slot>
        
        {{ $this->getEditForm() }}
        
        <x-slot name="footer">
            <x-filament::button wire:click="updatePartner">
                저장
            </x-filament::button>
            
            <x-filament::button color="gray" x-on:click="close">
                취소
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
    
    <x-filament-actions::modals />
</x-filament-panels::page>