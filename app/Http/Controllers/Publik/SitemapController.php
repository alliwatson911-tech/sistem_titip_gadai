<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            url('/') => '1.0',
            route('publik.layanan') => '0.8',
            route('publik.standar') => '0.8',
            route('publik.panduan') => '0.8',
            route('publik.syarat') => '0.8',
            route('publik.simulasi') => '0.8',
            route('publik.faq') => '0.8',
            route('publik.tentang') => '0.8',
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $loc => $priority) {
            $xml .= '  <url><loc>'.e($loc).'</loc><changefreq>weekly</changefreq><priority>'.$priority.'</priority></url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
