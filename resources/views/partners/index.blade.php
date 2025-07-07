<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <style>
        .filter-btn {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .filter-btn:hover {
            background-color: #d1d5db;
        }
        
        .filter-btn.active {
            background-color: #3b82f6;
            color: white;
        }
        
        .leaflet-popup-content {
            margin: 13px 19px;
            line-height: 1.5;
        }
        
        .popup-title {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 12px;
        }
        
        .partner-info {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .partner-info:last-child {
            border-bottom: none;
        }
        
        .partner-name {
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .partner-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 8px;
        }
        
        .partner-type.marketing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .partner-type.clinical {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .country-layer {
            cursor: pointer;
        }
        
        .country-layer:hover {
            fill-opacity: 0.9 !important;
        }
    </style>

    <div class="min-h-screen bg-white">
        <!-- Hero Section -->
        <div class="relative {{ isset($bannerSettings['image']) && $bannerSettings['image'] ? '' : 'bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600' }} overflow-hidden">
            @if(isset($bannerSettings['image']) && $bannerSettings['image'])
                <!-- Banner Image -->
                <div class="absolute inset-0">
                    <img src="{{ Storage::url($bannerSettings['image']) }}" alt="Partners Banner" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                </div>
            @else
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                </div>
            @endif
            
            <!-- Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <h1 class="text-5xl md:text-6xl font-bold text-white text-center mb-4">
                    {{ $bannerSettings['title'] ?? '글로벌 파트너사' }}
                </h1>
                @if(isset($bannerSettings['subtitle']) && $bannerSettings['subtitle'])
                    <p class="text-2xl md:text-3xl text-white text-center opacity-90">
                        {{ $bannerSettings['subtitle'] }}
                    </p>
                @endif
            </div>
            
            <!-- Tab Navigation for Continents -->
            <div class="bg-black bg-opacity-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="relative flex items-center">
                        <!-- Left Arrow -->
                        <button onclick="scrollTabs('left')" class="absolute left-0 z-10 p-2 text-white hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Tabs Container -->
                        <div class="overflow-hidden mx-8">
                            <div id="tabsContainer" class="flex transition-transform duration-300 ease-in-out">
                                @php
                                    $continents = [
                                        'all' => '전체',
                                        'asia' => '아시아',
                                        'europe' => '유럽',
                                        'north_america' => '북아메리카',
                                        'south_america' => '남아메리카',
                                        'africa' => '아프리카',
                                        'oceania' => '오세아니아'
                                    ];
                                    $currentContinent = request()->get('continent', 'all');
                                @endphp
                                
                                @foreach($continents as $key => $name)
                                    <button onclick="filterByContinent('{{ $key }}')" 
                                           class="continent-tab flex-shrink-0 px-6 py-4 text-center transition-all duration-300 {{ $currentContinent === $key ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white' }}"
                                           data-continent="{{ $key }}">
                                        <span class="whitespace-nowrap">{{ $name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Right Arrow -->
                        <button onclick="scrollTabs('right')" class="absolute right-0 z-10 p-2 text-white hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="partners-page">
            <div class="container mx-auto px-4 py-8">
                <div class="mb-8">
                    <div class="flex flex-wrap gap-4 justify-center">
                        <button class="filter-btn active px-6 py-2 rounded-full font-medium transition-colors" data-filter="all">
                            전체 ({{ count($partners) }})
                        </button>
                        <button class="filter-btn px-6 py-2 rounded-full font-medium transition-colors" data-filter="marketing">
                            마케팅 파트너 ({{ count($marketingPartners) }})
                        </button>
                        <button class="filter-btn px-6 py-2 rounded-full font-medium transition-colors" data-filter="clinical">
                            임상 파트너 ({{ count($clinicalPartners) }})
                        </button>
                    </div>
                </div>
                
                <div id="map" style="height: 600px; width: 100%; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"></div>
                
                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-6">파트너사 목록</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="partners-list">
                        @foreach($partners as $partner)
                        <div class="partner-card bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow" 
                             data-type="{{ $partner['type'] }}"
                             data-country="{{ $partner['country_code'] ?? '' }}"
                             data-continent="{{ $partner['continent_key'] ?? '' }}">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-semibold">{{ $partner['name'] }}</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $partner['type'] === 'marketing' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $partner['type'] === 'marketing' ? '마케팅' : '임상' }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $partner['country'] ?? '-' }}</span>
                                </div>
                                
                                @if(!empty($partner['description']))
                                <p class="mt-3 text-gray-700">{{ $partner['description'] }}</p>
                                @endif
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium locate-btn"
                                        data-country="{{ $partner['country_code'] ?? '' }}"
                                        data-name="{{ $partner['name'] }}">
                                    지도에서 보기 →
                                </button>
                                
                                @if(!empty($partner['website']))
                                <a href="{{ $partner['website'] }}" target="_blank" 
                                   class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    홈페이지 방문 →
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
    // Tab scrolling functions
    function scrollTabs(direction) {
        const container = document.getElementById('tabsContainer');
        const scrollAmount = 200;
        
        if (direction === 'left') {
            container.scrollLeft -= scrollAmount;
        } else {
            container.scrollLeft += scrollAmount;
        }
    }

    // Continent filter function
    function filterByContinent(continent) {
        // Update active tab
        document.querySelectorAll('.continent-tab').forEach(tab => {
            if (tab.dataset.continent === continent) {
                tab.classList.add('text-white', 'border-b-2', 'border-white');
                tab.classList.remove('text-gray-300');
            } else {
                tab.classList.remove('text-white', 'border-b-2', 'border-white');
                tab.classList.add('text-gray-300');
            }
        });

        // Filter partner cards
        const partnerCards = document.querySelectorAll('.partner-card');
        partnerCards.forEach(card => {
            if (continent === 'all' || card.dataset.continent === continent) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Update map highlighting
        if (window.updateMapByContinent) {
            window.updateMapByContinent(continent);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 지도 초기화
        const map = L.map('map', {
            center: [20, 0],
            zoom: 2,
            minZoom: 2, // 최소 줌 레벨 설정
            maxZoom: 2, // 최대 줌 레벨 설정 (확대/축소 비활성화)
            zoomControl: false, // 줌 컨트롤 숨기기
            scrollWheelZoom: false, // 스크롤 줌 비활성화
            doubleClickZoom: false, // 더블클릭 줌 비활성화
            touchZoom: false, // 터치 줌 비활성화
            maxBounds: [[-60, -180], [85, 180]], // 남극 제외한 경계 설정
            maxBoundsViscosity: 1.0 // 경계를 벗어나지 못하도록 설정
        });
        
        // 타일 레이어 추가
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 2,
            minZoom: 2,
            noWrap: true, // 지도가 반복되지 않도록 설정
            bounds: [[-60, -180], [85, 180]] // 남극 제외한 경계 설정
        }).addTo(map);
        
        // 파트너 데이터
        const partners = @json($partners);
        console.log('Partners data:', partners);
        let countryLayers = {};
        let currentFilter = 'all';
        let currentContinent = 'all';
        
        // 국가별 파트너 그룹화
        const partnersByCountry = {};
        partners.forEach(partner => {
            const countryCode = partner.country_code;
            console.log(`Partner: ${partner.name}, Country Code: ${countryCode}`);
            if (!countryCode) {
                console.warn(`No country code for partner: ${partner.name}`);
                return;
            }
            
            if (!partnersByCountry[countryCode]) {
                partnersByCountry[countryCode] = {
                    marketing: [],
                    clinical: [],
                    country: partner.country,
                    continent: partner.continent_key
                };
            }
            
            partnersByCountry[countryCode][partner.type].push(partner);
        });
        console.log('Partners by country:', partnersByCountry);
        
        // GeoJSON 데이터 로드 및 국가 레이어 생성
        console.log('Loading GeoJSON data...');
        fetch('https://raw.githubusercontent.com/datasets/geo-countries/master/data/countries.geojson')
            .then(response => {
                console.log('GeoJSON response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('GeoJSON data loaded:', data);
                console.log('Number of features:', data.features ? data.features.length : 0);
                console.log('Creating GeoJSON layer...');
                
                // GeoJSON의 모든 국가 코드 확인
                const availableCountryCodes = new Set();
                data.features.forEach(feature => {
                    const code = feature.properties['ISO3166-1-Alpha-2'] || feature.properties.ISO_A2;
                    if (code) availableCountryCodes.add(code);
                });
                console.log('Available country codes in GeoJSON:', Array.from(availableCountryCodes));
                console.log('Our partner country codes:', Object.keys(partnersByCountry));
                
                const geoJsonLayer = L.geoJSON(data, {
                    filter: function(feature) {
                        // 남극 제외
                        const countryName = feature.properties.name || feature.properties.NAME || feature.properties.ADMIN;
                        return countryName !== 'Antarctica';
                    },
                    style: function(feature) {
                        // 다양한 속성에서 국가 코드 찾기
                        const countryCode = feature.properties['ISO3166-1-Alpha-2'] || 
                                           feature.properties.ISO_A2 || 
                                           feature.properties.iso_a2 || 
                                           feature.properties.ISO2;
                        const countryName = feature.properties.name || feature.properties.NAME || feature.properties.ADMIN;
                        const hasPartners = partnersByCountry[countryCode];
                        
                        // 일부 국가의 ISO 코드 확인
                        if (['South Korea', 'Korea', 'Republic of Korea', 'United States', 'United Kingdom', 'Germany', 'Japan', 'Singapore', 'Switzerland'].some(name => countryName && countryName.includes(name))) {
                            console.log(`Country: ${countryName}, ISO Code: ${countryCode}, Has Partners: ${!!hasPartners}`);
                            console.log('Feature properties:', feature.properties);
                        }
                        
                        if (!hasPartners) {
                            return {
                                fillColor: '#e5e7eb',
                                weight: 1,
                                opacity: 0.7,
                                color: 'white',
                                fillOpacity: 0.3
                            };
                        }
                        
                        // 파트너가 있는 국가 스타일
                        const hasMarketing = hasPartners.marketing.length > 0;
                        const hasClinical = hasPartners.clinical.length > 0;
                        
                        let fillColor = '#e5e7eb';
                        if (hasMarketing && hasClinical) {
                            fillColor = '#8b5cf6'; // 보라색 (둘 다)
                        } else if (hasMarketing) {
                            fillColor = '#3b82f6'; // 파란색 (마케팅)
                        } else if (hasClinical) {
                            fillColor = '#10b981'; // 초록색 (임상)
                        }
                        
                        return {
                            fillColor: fillColor,
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            dashArray: '',
                            fillOpacity: 0.7
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        // 다양한 속성에서 국가 코드 찾기
                        const countryCode = feature.properties['ISO3166-1-Alpha-2'] || 
                                           feature.properties.ISO_A2 || 
                                           feature.properties.iso_a2 || 
                                           feature.properties.ISO2;
                        const countryPartners = partnersByCountry[countryCode];
                        
                        if (countryPartners) {
                            countryLayers[countryCode] = layer;
                            layer.continent = countryPartners.continent; // Add continent info to layer
                            
                            // 팝업 내용 생성
                            let popupContent = `<div class="popup-content">`;
                            popupContent += `<h3 class="popup-title">${countryPartners.country}</h3>`;
                            
                            if (countryPartners.marketing.length > 0) {
                                popupContent += `<div class="partner-info">`;
                                popupContent += `<div class="font-medium text-blue-600 mb-2">마케팅 파트너</div>`;
                                countryPartners.marketing.forEach(partner => {
                                    popupContent += `<div class="partner-name">• ${partner.name}`;
                                    if (partner.website) {
                                        popupContent += ` <a href="${partner.website}" target="_blank" class="text-blue-500 text-sm">[방문]</a>`;
                                    }
                                    popupContent += `</div>`;
                                });
                                popupContent += `</div>`;
                            }
                            
                            if (countryPartners.clinical.length > 0) {
                                popupContent += `<div class="partner-info">`;
                                popupContent += `<div class="font-medium text-green-600 mb-2">임상 파트너</div>`;
                                countryPartners.clinical.forEach(partner => {
                                    popupContent += `<div class="partner-name">• ${partner.name}`;
                                    if (partner.website) {
                                        popupContent += ` <a href="${partner.website}" target="_blank" class="text-blue-500 text-sm">[방문]</a>`;
                                    }
                                    popupContent += `</div>`;
                                });
                                popupContent += `</div>`;
                            }
                            
                            popupContent += `</div>`;
                            
                            layer.bindPopup(popupContent, {
                                maxWidth: 300
                            });
                            
                            // 마우스 이벤트
                            layer.on({
                                mouseover: function(e) {
                                    const layer = e.target;
                                    layer.setStyle({
                                        weight: 3,
                                        color: '#666',
                                        fillOpacity: 0.9
                                    });
                                },
                                mouseout: function(e) {
                                    const layer = e.target;
                                    layer.setStyle({
                                        weight: 2,
                                        color: 'white',
                                        fillOpacity: 0.7
                                    });
                                },
                                click: function(e) {
                                    map.fitBounds(e.target.getBounds());
                                }
                            });
                        }
                    }
                });
                
                geoJsonLayer.addTo(map);
                console.log('GeoJSON layer added to map');
                console.log('Country layers created:', Object.keys(countryLayers));
            })
            .catch(error => {
                console.error('Error loading GeoJSON:', error);
                alert('지도 데이터를 불러오는데 실패했습니다.');
            });
        
        // 필터 기능
        const filterButtons = document.querySelectorAll('.filter-btn');
        const partnerCards = document.querySelectorAll('.partner-card');
        
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // 버튼 활성화 상태 변경
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                currentFilter = this.dataset.filter;
                
                // 카드 필터링
                updateCardVisibility();
                
                // 지도 레이어 업데이트
                updateMapLayers();
            });
        });

        // Update card visibility based on both filters
        function updateCardVisibility() {
            partnerCards.forEach(card => {
                const typeMatch = currentFilter === 'all' || card.dataset.type === currentFilter;
                const continentMatch = currentContinent === 'all' || card.dataset.continent === currentContinent;
                
                if (typeMatch && continentMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // 지도 레이어 업데이트 함수
        function updateMapLayers() {
            Object.keys(countryLayers).forEach(countryCode => {
                const layer = countryLayers[countryCode];
                const countryPartners = partnersByCountry[countryCode];
                
                if (!countryPartners) return;
                
                const hasMarketing = countryPartners.marketing.length > 0;
                const hasClinical = countryPartners.clinical.length > 0;
                const continentMatch = currentContinent === 'all' || layer.continent === currentContinent;
                
                let fillColor = '#e5e7eb';
                let show = false;
                
                if (continentMatch) {
                    if (currentFilter === 'all') {
                        show = hasMarketing || hasClinical;
                        if (hasMarketing && hasClinical) {
                            fillColor = '#8b5cf6';
                        } else if (hasMarketing) {
                            fillColor = '#3b82f6';
                        } else if (hasClinical) {
                            fillColor = '#10b981';
                        }
                    } else if (currentFilter === 'marketing') {
                        show = hasMarketing;
                        fillColor = '#3b82f6';
                    } else if (currentFilter === 'clinical') {
                        show = hasClinical;
                        fillColor = '#10b981';
                    }
                }
                
                if (show) {
                    layer.setStyle({
                        fillColor: fillColor,
                        fillOpacity: 0.7,
                        weight: 2
                    });
                } else {
                    layer.setStyle({
                        fillColor: '#e5e7eb',
                        fillOpacity: 0.3,
                        weight: 1
                    });
                }
            });
        }

        // Update map by continent
        window.updateMapByContinent = function(continent) {
            currentContinent = continent;
            updateCardVisibility();
            updateMapLayers();
        };
        
        // 지도에서 보기 버튼
        document.querySelectorAll('.locate-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const countryCode = this.dataset.country;
                
                if (countryLayers[countryCode]) {
                    const layer = countryLayers[countryCode];
                    map.fitBounds(layer.getBounds());
                    layer.openPopup();
                }
            });
        });
    });
    </script>
</x-app-layout>