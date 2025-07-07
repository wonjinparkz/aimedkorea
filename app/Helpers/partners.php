<?php

use App\Models\ProjectOption;
use Illuminate\Support\Str;

/**
 * Get continents
 * 
 * @return array
 */
function get_continents() {
    return [
        'asia' => '아시아',
        'europe' => '유럽',
        'north_america' => '북아메리카',
        'south_america' => '남아메리카',
        'africa' => '아프리카',
        'oceania' => '오세아니아',
    ];
}

/**
 * Get country to continent mapping
 * 
 * @return array
 */
function get_country_continent_map() {
    return [
        // 아시아
        'KR' => 'asia',
        'CN' => 'asia',
        'JP' => 'asia',
        'IN' => 'asia',
        'SG' => 'asia',
        'TH' => 'asia',
        'MY' => 'asia',
        'ID' => 'asia',
        'VN' => 'asia',
        'PH' => 'asia',
        'TR' => 'asia',
        'SA' => 'asia',
        'AE' => 'asia',
        'IL' => 'asia',
        'HK' => 'asia',
        'TW' => 'asia',
        'BD' => 'asia',
        'PK' => 'asia',
        'IR' => 'asia',
        'IQ' => 'asia',
        
        // 유럽
        'DE' => 'europe',
        'GB' => 'europe',
        'FR' => 'europe',
        'IT' => 'europe',
        'ES' => 'europe',
        'CH' => 'europe',
        'SE' => 'europe',
        'NL' => 'europe',
        'BE' => 'europe',
        'AT' => 'europe',
        'DK' => 'europe',
        'NO' => 'europe',
        'FI' => 'europe',
        'PL' => 'europe',
        'RU' => 'europe',
        'PT' => 'europe',
        'GR' => 'europe',
        'CZ' => 'europe',
        'HU' => 'europe',
        'RO' => 'europe',
        'UA' => 'europe',
        'IE' => 'europe',
        
        // 북아메리카
        'US' => 'north_america',
        'CA' => 'north_america',
        'MX' => 'north_america',
        
        // 남아메리카
        'BR' => 'south_america',
        'AR' => 'south_america',
        'CL' => 'south_america',
        'CO' => 'south_america',
        'PE' => 'south_america',
        'VE' => 'south_america',
        'EC' => 'south_america',
        'UY' => 'south_america',
        'PY' => 'south_america',
        'BO' => 'south_america',
        
        // 아프리카
        'EG' => 'africa',
        'ZA' => 'africa',
        'NG' => 'africa',
        'KE' => 'africa',
        'MA' => 'africa',
        'ET' => 'africa',
        'GH' => 'africa',
        'TZ' => 'africa',
        'DZ' => 'africa',
        'TN' => 'africa',
        
        // 오세아니아
        'AU' => 'oceania',
        'NZ' => 'oceania',
        'FJ' => 'oceania',
        'PG' => 'oceania',
    ];
}

/**
 * Get continent for a country code
 * 
 * @param string $country_code
 * @return string|null
 */
function get_continent_by_country($country_code) {
    $map = get_country_continent_map();
    return $map[$country_code] ?? null;
}

/**
 * Get country list with codes
 * 
 * @return array
 */
function get_countries() {
    return [
        // 아시아
        'KR' => '대한민국',
        'CN' => '중국',
        'JP' => '일본',
        'IN' => '인도',
        'SG' => '싱가포르',
        'TH' => '태국',
        'MY' => '말레이시아',
        'ID' => '인도네시아',
        'VN' => '베트남',
        'PH' => '필리핀',
        'TR' => '터키',
        'SA' => '사우디아라비아',
        'AE' => '아랍에미리트',
        'IL' => '이스라엘',
        'HK' => '홍콩',
        'TW' => '대만',
        'BD' => '방글라데시',
        'PK' => '파키스탄',
        'IR' => '이란',
        'IQ' => '이라크',
        
        // 유럽
        'DE' => '독일',
        'GB' => '영국',
        'FR' => '프랑스',
        'IT' => '이탈리아',
        'ES' => '스페인',
        'CH' => '스위스',
        'SE' => '스웨덴',
        'NL' => '네덜란드',
        'BE' => '벨기에',
        'AT' => '오스트리아',
        'DK' => '덴마크',
        'NO' => '노르웨이',
        'FI' => '핀란드',
        'PL' => '폴란드',
        'RU' => '러시아',
        'PT' => '포르투갈',
        'GR' => '그리스',
        'CZ' => '체코',
        'HU' => '헝가리',
        'RO' => '루마니아',
        'UA' => '우크라이나',
        'IE' => '아일랜드',
        
        // 북아메리카
        'US' => '미국',
        'CA' => '캐나다',
        'MX' => '멕시코',
        
        // 남아메리카
        'BR' => '브라질',
        'AR' => '아르헨티나',
        'CL' => '칠레',
        'CO' => '콜롬비아',
        'PE' => '페루',
        'VE' => '베네수엘라',
        'EC' => '에콰도르',
        'UY' => '우루과이',
        'PY' => '파라과이',
        'BO' => '볼리비아',
        
        // 아프리카
        'EG' => '이집트',
        'ZA' => '남아프리카공화국',
        'NG' => '나이지리아',
        'KE' => '케냐',
        'MA' => '모로코',
        'ET' => '에티오피아',
        'GH' => '가나',
        'TZ' => '탄자니아',
        'DZ' => '알제리',
        'TN' => '튀니지',
        
        // 오세아니아
        'AU' => '호주',
        'NZ' => '뉴질랜드',
        'FJ' => '피지',
        'PG' => '파푸아뉴기니',
    ];
}

/**
 * Get countries grouped by continent
 * 
 * @return array
 */
function get_countries_by_continent() {
    $countries = get_countries();
    $continents = get_continents();
    $continent_map = get_country_continent_map();
    
    $grouped = [];
    
    // Initialize continents
    foreach ($continents as $continent_key => $continent_name) {
        $grouped[$continent_name] = [];
    }
    
    // Group countries by continent
    foreach ($countries as $code => $name) {
        $continent_key = $continent_map[$code] ?? null;
        if ($continent_key && isset($continents[$continent_key])) {
            $grouped[$continents[$continent_key]][$code] = $name;
        }
    }
    
    // Remove empty continents
    return array_filter($grouped, function($countries) {
        return !empty($countries);
    });
}

/**
 * Get all partners or partners by type
 * 
 * @param string|null $type 'marketing', 'clinical', or null for all
 * @return array
 */
function get_partners($type = null) {
    $marketing_partners = ProjectOption::get('marketing_partners', []);
    $clinical_partners = ProjectOption::get('clinical_partners', []);
    
    if (!is_array($marketing_partners)) {
        $marketing_partners = [];
    }
    
    if (!is_array($clinical_partners)) {
        $clinical_partners = [];
    }
    
    if ($type === 'marketing') {
        return $marketing_partners;
    }
    
    if ($type === 'clinical') {
        return $clinical_partners;
    }
    
    // Return all partners with their types
    $all_partners = [];
    
    foreach ($marketing_partners as $partner) {
        $partner['type'] = 'marketing';
        $all_partners[] = $partner;
    }
    
    foreach ($clinical_partners as $partner) {
        $partner['type'] = 'clinical';
        $all_partners[] = $partner;
    }
    
    return $all_partners;
}

/**
 * Get a specific partner by ID
 * 
 * @param string $id
 * @return array|null
 */
function get_partner($id) {
    $all_partners = get_partners();
    
    foreach ($all_partners as $partner) {
        if (isset($partner['id']) && $partner['id'] === $id) {
            return $partner;
        }
    }
    
    return null;
}

/**
 * Add a new partner
 * 
 * @param array $partner_data
 * @return bool
 */
function add_partner($partner_data) {
    if (!isset($partner_data['type']) || !in_array($partner_data['type'], ['marketing', 'clinical'])) {
        return false;
    }
    
    if (!isset($partner_data['name']) || (!isset($partner_data['country']) && !isset($partner_data['location']['country']))) {
        return false;
    }
    
    // Generate unique ID
    $partner_data['id'] = Str::uuid()->toString();
    $partner_data['created_at'] = now()->toIso8601String();
    $partner_data['updated_at'] = now()->toIso8601String();
    
    // Add continent information based on country code
    if (!empty($partner_data['country_code'])) {
        $continent_key = get_continent_by_country($partner_data['country_code']);
        if ($continent_key) {
            $continents = get_continents();
            $partner_data['continent'] = $continents[$continent_key] ?? null;
            $partner_data['continent_key'] = $continent_key;
        }
    }
    
    $type = $partner_data['type'];
    $option_key = $type . '_partners';
    
    $partners = ProjectOption::get($option_key, []);
    if (!is_array($partners)) {
        $partners = [];
    }
    
    $partners[] = $partner_data;
    
    return ProjectOption::set($option_key, $partners);
}

/**
 * Update an existing partner
 * 
 * @param string $id
 * @param array $partner_data
 * @return bool
 */
function update_partner($id, $partner_data) {
    $existing_partner = get_partner($id);
    
    if (!$existing_partner) {
        return false;
    }
    
    $type = $existing_partner['type'];
    $option_key = $type . '_partners';
    
    $partners = ProjectOption::get($option_key, []);
    if (!is_array($partners)) {
        return false;
    }
    
    foreach ($partners as $index => $partner) {
        if (isset($partner['id']) && $partner['id'] === $id) {
            // Preserve ID and type
            $partner_data['id'] = $id;
            $partner_data['type'] = $type;
            $partner_data['created_at'] = $partner['created_at'] ?? now()->toIso8601String();
            $partner_data['updated_at'] = now()->toIso8601String();
            
            // Update continent information based on country code
            if (!empty($partner_data['country_code'])) {
                $continent_key = get_continent_by_country($partner_data['country_code']);
                if ($continent_key) {
                    $continents = get_continents();
                    $partner_data['continent'] = $continents[$continent_key] ?? null;
                    $partner_data['continent_key'] = $continent_key;
                }
            }
            
            $partners[$index] = array_merge($partner, $partner_data);
            return ProjectOption::set($option_key, $partners);
        }
    }
    
    return false;
}

/**
 * Remove a partner
 * 
 * @param string $id
 * @return bool
 */
function remove_partner($id) {
    $existing_partner = get_partner($id);
    
    if (!$existing_partner) {
        return false;
    }
    
    $type = $existing_partner['type'];
    $option_key = $type . '_partners';
    
    $partners = ProjectOption::get($option_key, []);
    if (!is_array($partners)) {
        return false;
    }
    
    $filtered_partners = array_filter($partners, function($partner) use ($id) {
        return !isset($partner['id']) || $partner['id'] !== $id;
    });
    
    // Re-index array
    $filtered_partners = array_values($filtered_partners);
    
    return ProjectOption::set($option_key, $filtered_partners);
}

/**
 * Get partners grouped by country
 * 
 * @param string|null $type
 * @return array
 */
function get_partners_by_country($type = null) {
    $partners = get_partners($type);
    $grouped = [];
    
    foreach ($partners as $partner) {
        $country = $partner['country'] ?? ($partner['location']['country'] ?? 'Unknown');
        if (!isset($grouped[$country])) {
            $grouped[$country] = [];
        }
        $grouped[$country][] = $partner;
    }
    
    return $grouped;
}

/**
 * Search partners by name or location
 * 
 * @param string $query
 * @param string|null $type
 * @return array
 */
function search_partners($query, $type = null) {
    $partners = get_partners($type);
    $query = strtolower($query);
    
    return array_filter($partners, function($partner) use ($query) {
        $name_match = isset($partner['name']) && str_contains(strtolower($partner['name']), $query);
        $country_match = (isset($partner['country']) && str_contains(strtolower($partner['country']), $query)) ||
                        (isset($partner['location']['country']) && str_contains(strtolower($partner['location']['country']), $query));
        
        return $name_match || $country_match;
    });
}

/**
 * Get partners grouped by continent
 * 
 * @param string|null $type
 * @return array
 */
function get_partners_by_continent($type = null) {
    $partners = get_partners($type);
    $continents = get_continents();
    $grouped = [];
    
    // Initialize continents
    foreach ($continents as $continent_key => $continent_name) {
        $grouped[$continent_name] = [];
    }
    
    // Add 'Other' category for partners without continent
    $grouped['기타'] = [];
    
    foreach ($partners as $partner) {
        $continent = $partner['continent'] ?? null;
        if ($continent && isset($grouped[$continent])) {
            $grouped[$continent][] = $partner;
        } else {
            $grouped['기타'][] = $partner;
        }
    }
    
    // Remove empty groups
    return array_filter($grouped, function($partners) {
        return !empty($partners);
    });
}