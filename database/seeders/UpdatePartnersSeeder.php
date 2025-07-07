<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdatePartnersSeeder extends Seeder
{
    public function run()
    {
        // 기존 파트너사 모두 삭제
        update_option('partners', []);
        
        // 새 파트너사 데이터
        $partners = [
            // Europe
            [
                'name' => 'EQT Life Sciences',
                'type' => 'marketing',
                'country' => '스웨덴',
                'continent' => '유럽',
                'website' => 'https://eqtgroup.com',
                'description' => 'EQT Life Sciences'
            ],
            [
                'name' => 'Sofinnova Partners',
                'type' => 'marketing',
                'country' => '프랑스',
                'continent' => '유럽',
                'website' => 'https://sofinnovapartners.com',
                'description' => 'Sofinnova Partners'
            ],
            [
                'name' => 'Atomico',
                'type' => 'marketing',
                'country' => '영국',
                'continent' => '유럽',
                'website' => 'https://www.atomico.com',
                'description' => 'Atomico'
            ],
            // North America
            [
                'name' => 'a16z Bio+Health',
                'type' => 'marketing',
                'country' => '미국',
                'continent' => '북미',
                'website' => 'https://a16z.com',
                'description' => 'a16z Bio+Health'
            ],
            [
                'name' => 'ARCH Venture Partners',
                'type' => 'marketing',
                'country' => '미국',
                'continent' => '북미',
                'website' => 'https://archventure.com',
                'description' => 'ARCH Venture Partners'
            ],
            [
                'name' => 'NEA',
                'type' => 'marketing',
                'country' => '미국',
                'continent' => '북미',
                'website' => 'https://nea.com',
                'description' => 'NEA'
            ],
            // Africa
            [
                'name' => 'Future Africa',
                'type' => 'marketing',
                'country' => '나이지리아',
                'continent' => '아프리카',
                'website' => 'https://www.future.africa',
                'description' => 'Future Africa'
            ],
            [
                'name' => 'Novastar Ventures',
                'type' => 'marketing',
                'country' => '케냐',
                'continent' => '아프리카',
                'website' => 'https://www.novastarventures.com',
                'description' => 'Novastar Ventures'
            ],
            [
                'name' => 'EchoVC Partners',
                'type' => 'marketing',
                'country' => '나이지리아',
                'continent' => '아프리카',
                'website' => 'https://www.echovc.com',
                'description' => 'EchoVC Partners'
            ],
            // Asia
            [
                'name' => 'SoftBank Vision Fund',
                'type' => 'marketing',
                'country' => '일본',
                'continent' => '아시아',
                'website' => 'https://visionfund.com',
                'description' => 'SoftBank Vision Fund'
            ],
            [
                'name' => 'Hillhouse Capital',
                'type' => 'marketing',
                'country' => '중국',
                'continent' => '아시아',
                'website' => 'https://www.hillhousecap.com',
                'description' => 'Hillhouse Capital'
            ],
            [
                'name' => 'Korea Investment Partners',
                'type' => 'marketing',
                'country' => '한국',
                'continent' => '아시아',
                'website' => 'https://www.kipvc.com',
                'description' => 'Korea Investment Partners'
            ],
            // Middle East
            [
                'name' => 'Mubadala Capital',
                'type' => 'marketing',
                'country' => 'UAE',
                'continent' => '중동',
                'website' => 'https://www.mubadala.com',
                'description' => 'Mubadala Capital'
            ],
            [
                'name' => 'Saudi Aramco Ventures',
                'type' => 'marketing',
                'country' => '사우디아라비아',
                'continent' => '중동',
                'website' => 'https://www.aramcoventures.com',
                'description' => 'Saudi Aramco Ventures'
            ],
            [
                'name' => 'Wadi Makkah Ventures',
                'type' => 'marketing',
                'country' => '사우디아라비아',
                'continent' => '중동',
                'website' => 'https://www.wadimakkah.sa',
                'description' => 'Wadi Makkah Ventures'
            ],
        ];
        
        // 새 파트너사 추가
        foreach ($partners as $partner) {
            add_partner(
                $partner['name'],
                $partner['type'],
                $partner['country'],
                $partner['continent'],
                $partner['website'],
                $partner['description']
            );
        }
        
        echo "파트너사가 성공적으로 업데이트되었습니다.\n";
        echo "총 " . count($partners) . "개의 파트너사가 추가되었습니다.\n";
    }
}