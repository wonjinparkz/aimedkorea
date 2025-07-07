<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectOption;

class PartnersController extends Controller
{
    public function index()
    {
        $partners = get_partners();
        
        // Get banner settings
        $bannerSettings = ProjectOption::get('partners_banner_settings', [
            'title' => '글로벌 파트너사',
            'subtitle' => 'Global Partner',
            'image' => null
        ]);
        
        return view('partners.index', [
            'partners' => $partners,
            'marketingPartners' => get_partners('marketing'),
            'clinicalPartners' => get_partners('clinical'),
            'bannerSettings' => $bannerSettings,
        ]);
    }
}