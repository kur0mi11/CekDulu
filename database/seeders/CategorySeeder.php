<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Penipuan Jual Beli Online',
            'Penipuan Investasi Bodong',
            'Phishing / Link Palsu',
            'Penipuan Lowongan Kerja',
            'Pinjaman Online Ilegal',
            'Lainnya'
        ];
        
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}