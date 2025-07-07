<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class DummyPostSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $this->command->error('No user found. Please create a user first.');
            return;
        }

        // 상품 더미 데이터
        $products = [
            [
                'title' => 'AI 헬스케어 스마트 워치',
                'summary' => '최첨단 AI 기술로 건강을 실시간 모니터링하는 스마트 워치',
                'content' => '<p>AI 헬스케어 스마트 워치는 최신 인공지능 기술을 활용하여 사용자의 건강 상태를 24시간 모니터링합니다.</p><h3>주요 기능</h3><ul><li>심박수 실시간 측정</li><li>수면 패턴 분석</li><li>운동량 자동 추적</li><li>스트레스 지수 측정</li></ul><p>의료진과 연동하여 건강 이상 징후를 조기에 발견할 수 있습니다.</p>',
            ],
            [
                'title' => '루카 스마트 체중계',
                'summary' => 'AI가 분석하는 체성분 측정 스마트 체중계',
                'content' => '<p>단순한 체중 측정을 넘어 체성분을 정밀하게 분석하는 스마트 체중계입니다.</p><h3>측정 항목</h3><ul><li>체중, BMI</li><li>체지방률</li><li>근육량</li><li>기초대사량</li></ul><p>앱과 연동하여 체중 변화 추이를 한눈에 확인할 수 있습니다.</p>',
            ],
            [
                'title' => 'AI 자세 교정 쿠션',
                'summary' => '실시간 자세 분석과 교정을 도와주는 스마트 쿠션',
                'content' => '<p>압력 센서와 AI 알고리즘으로 앉은 자세를 실시간 분석합니다.</p><h3>특징</h3><ul><li>자세 불균형 알림</li><li>장시간 착석 경고</li><li>스트레칭 가이드 제공</li></ul><p>올바른 자세 유지로 허리 건강을 지켜보세요.</p>',
            ],
            [
                'title' => '휴대용 공기질 측정기',
                'summary' => 'AI가 분석하는 실내외 공기질 모니터링 디바이스',
                'content' => '<p>미세먼지, 이산화탄소, 유해가스 등을 실시간으로 측정합니다.</p><h3>측정 요소</h3><ul><li>PM2.5, PM10</li><li>CO2 농도</li><li>VOC (휘발성유기화합물)</li><li>온도 및 습도</li></ul><p>건강한 생활 환경을 위한 필수 아이템입니다.</p>',
            ],
            [
                'title' => 'AI 수면 최적화 베개',
                'summary' => '개인 맞춤형 수면 환경을 제공하는 스마트 베개',
                'content' => '<p>수면 패턴을 분석하여 최적의 높이와 경도를 자동 조절합니다.</p><h3>기능</h3><ul><li>자동 높이 조절</li><li>온도 조절 기능</li><li>코골이 감지 및 완화</li><li>수면 질 분석 리포트</li></ul><p>깊고 편안한 수면을 경험해보세요.</p>',
            ],
            [
                'title' => '스마트 약 복용 알리미',
                'summary' => 'AI 기반 복약 관리 시스템',
                'content' => '<p>정확한 시간에 약 복용을 알려주고 복약 이력을 관리합니다.</p><h3>주요 기능</h3><ul><li>복약 시간 알림</li><li>약물 상호작용 체크</li><li>복약 이력 기록</li><li>가족 공유 기능</li></ul><p>안전하고 체계적인 복약 관리를 시작하세요.</p>',
            ],
        ];

        foreach ($products as $index => $product) {
            Post::create([
                'title' => $product['title'],
                'slug' => Str::slug($product['title']) . '-' . uniqid(),
                'type' => 'product',
                'summary' => $product['summary'],
                'content' => $product['content'],
                'author_id' => $user->id,
                'is_published' => true,
                'published_at' => now()->subDays(30 - $index * 5),
                'image' => 'posts/product-' . ($index + 1) . '.jpg',
            ]);
        }

        // 식품 더미 데이터
        $foods = [
            [
                'title' => 'AI 맞춤 영양제 세트',
                'summary' => '개인 건강 데이터 기반 맞춤형 영양제 구성',
                'content' => '<p>AI가 분석한 건강 데이터를 바탕으로 개인에게 필요한 영양소를 선별합니다.</p><h3>구성</h3><ul><li>종합 비타민</li><li>오메가-3</li><li>프로바이오틱스</li><li>항산화제</li></ul><p>건강한 일상을 위한 맞춤 영양 관리를 시작하세요.</p>',
            ],
            [
                'title' => '루카 프로틴 쉐이크',
                'summary' => 'AI가 추천하는 운동 후 최적의 단백질 보충제',
                'content' => '<p>운동 강도와 목표에 맞춰 AI가 추천하는 프로틴 쉐이크입니다.</p><h3>특징</h3><ul><li>고품질 유청 단백질</li><li>BCAA 함유</li><li>저칼로리 포뮬러</li><li>다양한 맛</li></ul><p>효과적인 근육 회복과 성장을 도와드립니다.</p>',
            ],
            [
                'title' => '스마트 다이어트 도시락',
                'summary' => 'AI 영양사가 설계한 칼로리 조절 도시락',
                'content' => '<p>개인의 기초대사량과 활동량을 고려한 맞춤형 도시락입니다.</p><h3>메뉴 구성</h3><ul><li>균형잡힌 영양소</li><li>신선한 재료</li><li>칼로리 표시</li><li>주간 식단 플랜</li></ul><p>건강한 다이어트를 도와드립니다.</p>',
            ],
            [
                'title' => 'AI 건강 주스 패키지',
                'summary' => '건강 상태에 맞춘 맞춤형 건강 주스',
                'content' => '<p>AI가 분석한 건강 데이터로 만든 개인 맞춤 건강 주스입니다.</p><h3>종류</h3><ul><li>면역력 강화 주스</li><li>피로 회복 주스</li><li>디톡스 주스</li><li>항산화 주스</li></ul><p>매일 아침 건강한 하루를 시작하세요.</p>',
            ],
            [
                'title' => '기능성 건강 스낵바',
                'summary' => '영양소가 풍부한 건강 간식',
                'content' => '<p>바쁜 일상 속에서도 건강을 챙길 수 있는 기능성 스낵바입니다.</p><h3>영양 성분</h3><ul><li>고단백</li><li>저당</li><li>식이섬유 풍부</li><li>비타민 강화</li></ul><p>건강한 간식으로 에너지를 충전하세요.</p>',
            ],
            [
                'title' => '프리미엄 건강차 세트',
                'summary' => 'AI가 추천하는 체질별 맞춤 건강차',
                'content' => '<p>체질과 건강 상태에 맞는 프리미엄 건강차 세트입니다.</p><h3>구성품</h3><ul><li>녹차</li><li>홍차</li><li>허브차</li><li>한방차</li></ul><p>차 한잔의 여유로 건강을 관리하세요.</p>',
            ],
        ];

        foreach ($foods as $index => $food) {
            Post::create([
                'title' => $food['title'],
                'slug' => Str::slug($food['title']) . '-' . uniqid(),
                'type' => 'food',
                'summary' => $food['summary'],
                'content' => $food['content'],
                'author_id' => $user->id,
                'is_published' => true,
                'published_at' => now()->subDays(30 - $index * 5),
                'image' => 'posts/food-' . ($index + 1) . '.jpg',
            ]);
        }

        // 서비스 더미 데이터
        $services = [
            [
                'title' => 'AI 헬스케어 컨설팅',
                'summary' => '개인 맞춤형 AI 기반 건강 관리 컨설팅 서비스',
                'content' => '<p>AI 기술을 활용한 종합적인 건강 관리 컨설팅을 제공합니다.</p><h3>서비스 내용</h3><ul><li>건강 데이터 분석</li><li>맞춤형 건강 플랜 수립</li><li>정기적인 모니터링</li><li>전문가 상담</li></ul><p>체계적인 건강 관리를 시작하세요.</p>',
            ],
            [
                'title' => '원격 진료 서비스',
                'summary' => 'AI 의료진과 함께하는 비대면 진료 서비스',
                'content' => '<p>시간과 장소에 구애받지 않는 원격 진료 서비스입니다.</p><h3>특징</h3><ul><li>24시간 상담 가능</li><li>전문의 연결</li><li>처방전 발급</li><li>의료 기록 관리</li></ul><p>편리하고 안전한 의료 서비스를 경험하세요.</p>',
            ],
            [
                'title' => 'AI 운동 코칭 프로그램',
                'summary' => 'AI 트레이너와 함께하는 맞춤형 운동 프로그램',
                'content' => '<p>개인의 체력과 목표에 맞춘 AI 운동 코칭 서비스입니다.</p><h3>프로그램</h3><ul><li>체력 측정 및 분석</li><li>맞춤형 운동 계획</li><li>실시간 자세 교정</li><li>진도 추적 및 피드백</li></ul><p>효과적인 운동으로 건강한 몸을 만드세요.</p>',
            ],
            [
                'title' => '스마트 홈 헬스케어',
                'summary' => 'IoT 기기와 AI를 활용한 홈 헬스케어 시스템',
                'content' => '<p>집에서도 전문적인 건강 관리를 받을 수 있는 서비스입니다.</p><h3>구성</h3><ul><li>IoT 건강 기기 설치</li><li>실시간 건강 모니터링</li><li>응급 상황 알림</li><li>가족 케어 서비스</li></ul><p>안전하고 편안한 홈 헬스케어를 시작하세요.</p>',
            ],
            [
                'title' => 'AI 영양 관리 서비스',
                'summary' => '개인별 맞춤 영양 관리 및 식단 추천 서비스',
                'content' => '<p>AI 영양사가 제공하는 맞춤형 영양 관리 서비스입니다.</p><h3>서비스</h3><ul><li>영양 상태 분석</li><li>맞춤 식단 제공</li><li>식사 기록 관리</li><li>영양 상담</li></ul><p>균형잡힌 영양으로 건강을 지키세요.</p>',
            ],
            [
                'title' => '정신 건강 케어 서비스',
                'summary' => 'AI 기반 정신 건강 관리 및 상담 서비스',
                'content' => '<p>스트레스와 정신 건강을 관리하는 종합 케어 서비스입니다.</p><h3>프로그램</h3><ul><li>스트레스 측정</li><li>명상 가이드</li><li>심리 상담</li><li>수면 관리</li></ul><p>마음의 건강도 함께 챙기세요.</p>',
            ],
        ];

        foreach ($services as $index => $service) {
            Post::create([
                'title' => $service['title'],
                'slug' => Str::slug($service['title']) . '-' . uniqid(),
                'type' => 'service',
                'summary' => $service['summary'],
                'content' => $service['content'],
                'author_id' => $user->id,
                'is_published' => true,
                'published_at' => now()->subDays(30 - $index * 5),
                'image' => 'posts/service-' . ($index + 1) . '.jpg',
            ]);
        }

        // 홍보 더미 데이터
        $promotions = [
            [
                'title' => 'AI 헬스케어 엑스포 2024 참가',
                'summary' => '최신 AI 헬스케어 기술을 한자리에서 만나보세요',
                'content' => '<p>2024년 최대 규모의 AI 헬스케어 엑스포에 참가합니다.</p><h3>전시 내용</h3><ul><li>최신 AI 헬스케어 솔루션</li><li>스마트 의료 기기</li><li>원격 진료 시스템</li><li>건강 관리 플랫폼</li></ul><p>미래 의료 기술의 혁신을 직접 체험해보세요.</p>',
            ],
            [
                'title' => '루카 헬스케어 신제품 런칭 이벤트',
                'summary' => '혁신적인 AI 헬스케어 제품 출시 기념 특별 이벤트',
                'content' => '<p>루카 헬스케어의 새로운 제품 출시를 기념하는 특별 이벤트입니다.</p><h3>이벤트 혜택</h3><ul><li>신제품 50% 할인</li><li>무료 체험 기회</li><li>전문가 상담</li><li>경품 추첨</li></ul><p>놓치지 마세요! 선착순 한정 혜택입니다.</p>',
            ],
            [
                'title' => '글로벌 파트너십 체결 소식',
                'summary' => '세계적인 헬스케어 기업과의 전략적 제휴',
                'content' => '<p>글로벌 헬스케어 리더와 전략적 파트너십을 체결했습니다.</p><h3>협력 분야</h3><ul><li>AI 기술 공동 개발</li><li>글로벌 시장 진출</li><li>연구 개발 협력</li><li>인재 교류</li></ul><p>더 나은 헬스케어 서비스를 제공하겠습니다.</p>',
            ],
            [
                'title' => '무료 건강 검진 캠페인',
                'summary' => 'AI 기반 무료 건강 검진 서비스 제공',
                'content' => '<p>지역 사회를 위한 무료 건강 검진 캠페인을 실시합니다.</p><h3>검진 항목</h3><ul><li>기초 건강 검사</li><li>체성분 분석</li><li>스트레스 측정</li><li>건강 상담</li></ul><p>건강한 사회를 만들어가는데 함께해주세요.</p>',
            ],
            [
                'title' => 'AI 헬스케어 세미나 개최',
                'summary' => '전문가와 함께하는 AI 헬스케어 기술 세미나',
                'content' => '<p>최신 AI 헬스케어 트렌드와 기술을 공유하는 세미나입니다.</p><h3>주요 주제</h3><ul><li>AI in Healthcare</li><li>디지털 헬스케어 혁신</li><li>미래 의료 기술</li><li>성공 사례 공유</li></ul><p>헬스케어의 미래를 함께 그려보세요.</p>',
            ],
            [
                'title' => '고객 감사 이벤트',
                'summary' => '고객님께 감사의 마음을 전하는 특별 이벤트',
                'content' => '<p>항상 함께해주신 고객님들께 감사드립니다.</p><h3>이벤트 내용</h3><ul><li>전 제품 20% 할인</li><li>1+1 이벤트</li><li>무료 배송</li><li>VIP 멤버십 혜택</li></ul><p>감사의 마음을 담은 특별한 혜택을 누려보세요.</p>',
            ],
        ];

        foreach ($promotions as $index => $promotion) {
            Post::create([
                'title' => $promotion['title'],
                'slug' => Str::slug($promotion['title']) . '-' . uniqid(),
                'type' => 'promotion',
                'summary' => $promotion['summary'],
                'content' => $promotion['content'],
                'author_id' => $user->id,
                'is_published' => true,
                'published_at' => now()->subDays(30 - $index * 5),
                'image' => 'posts/promotion-' . ($index + 1) . '.jpg',
            ]);
        }

        $this->command->info('Dummy posts created successfully!');
    }
}