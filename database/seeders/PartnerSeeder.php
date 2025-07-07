<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 샘플 마케팅 파트너사 데이터
        $marketingPartners = [
            [
                'name' => 'Global Marketing Solutions',
                'country_code' => 'KR',
                'country' => '대한민국',
                'type' => 'marketing',
                'description' => '아시아 지역 주요 마케팅 파트너사',
                'website' => 'https://www.gms-korea.com',
            ],
            [
                'name' => 'Digital Health Marketing Inc.',
                'country_code' => 'US',
                'country' => '미국',
                'type' => 'marketing',
                'description' => '북미 지역 디지털 헬스케어 마케팅 전문',
                'website' => 'https://www.dhm-inc.com',
            ],
            [
                'name' => 'European Medical Promotions',
                'country_code' => 'GB',
                'country' => '영국',
                'type' => 'marketing',
                'description' => '유럽 시장 의료 마케팅 파트너',
                'website' => 'https://www.emp-uk.com',
            ],
            [
                'name' => 'Asia Pacific Healthcare Marketing',
                'country_code' => 'SG',
                'country' => '싱가포르',
                'type' => 'marketing',
                'description' => '동남아시아 헬스케어 마케팅 전문',
                'website' => 'https://www.aphm.sg',
            ],
        ];
        
        // 샘플 임상 파트너사 데이터
        $clinicalPartners = [
            [
                'name' => 'Seoul National University Hospital',
                'country_code' => 'KR',
                'country' => '대한민국',
                'type' => 'clinical',
                'description' => '국내 최고 수준의 임상시험 센터',
                'website' => 'https://www.snuh.org',
            ],
            [
                'name' => 'Johns Hopkins Medicine',
                'country_code' => 'US',
                'country' => '미국',
                'type' => 'clinical',
                'description' => '세계적인 의료 연구 기관',
                'website' => 'https://www.hopkinsmedicine.org',
            ],
            [
                'name' => 'University Hospital Zurich',
                'country_code' => 'CH',
                'country' => '스위스',
                'type' => 'clinical',
                'description' => '유럽 주요 임상 연구 센터',
                'website' => 'https://www.usz.ch',
            ],
            [
                'name' => 'Singapore General Hospital',
                'country_code' => 'SG',
                'country' => '싱가포르',
                'type' => 'clinical',
                'description' => '동남아시아 임상시험 허브',
                'website' => 'https://www.sgh.com.sg',
            ],
            [
                'name' => 'Tokyo Medical University',
                'country_code' => 'JP',
                'country' => '일본',
                'type' => 'clinical',
                'description' => '일본 최대 규모의 임상 연구 기관',
                'website' => 'https://www.tokyo-med.ac.jp',
            ],
            [
                'name' => 'Charité – Universitätsmedizin Berlin',
                'country_code' => 'DE',
                'country' => '독일',
                'type' => 'clinical',
                'description' => '유럽 최대 규모의 대학 병원',
                'website' => 'https://www.charite.de',
            ],
        ];
        
        // 기존 데이터 삭제
        \App\Models\ProjectOption::where('option_name', 'marketing_partners')->delete();
        \App\Models\ProjectOption::where('option_name', 'clinical_partners')->delete();
        
        // 마케팅 파트너사 추가
        foreach ($marketingPartners as $partner) {
            add_partner($partner);
        }
        
        // 임상 파트너사 추가
        foreach ($clinicalPartners as $partner) {
            add_partner($partner);
        }
        
        $this->command->info('Partner data seeded successfully!');
    }
}