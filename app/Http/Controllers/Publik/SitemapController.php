<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            url('/') => '1.0',
            route('publik.tentang') => '0.8',
            route('publik.sambutan') => '0.7',
            route('publik.visi-misi') => '0.7',
            route('publik.legalitas') => '0.7',
            route('publik.cabang') => '0.8',
            route('publik.layanan') => '0.9',
            route('publik.simulasi') => '0.9',
            route('publik.cara-penitipan') => '0.8',
            route('publik.syarat') => '0.8',
            route('publik.promo') => '0.8',
            route('publik.kontak') => '0.9',
        ];

        try {
            $products = Product::clientFeatured()->aktif()->get();
            foreach ($products as $p) {
                $urls[route('publik.layanan.detail', $p->slug)] = '0.7';
            }
        } catch (\Throwable $e) {
            // Fallback gracefully jika database belum siap
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $loc => $priority) {
            $xml .= '  <url><loc>'.e($loc).'</loc><changefreq>weekly</changefreq><priority>'.$priority.'</priority></url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
