<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Supplier::count() > 0) {
            return;
        }

        $rows = [
            ['ogit', 'AgroKimyo Savdo MChJ', '+998 90 123 45 67', 'Jizzax sh., Sanoat ko\'chasi 14', null, "Organik va mineral o'g'itlar"],
            ['ogit', "Fermer Ta'minot", '+998 91 234 56 78', "Jizzax viloyati, Do'stlik tumani", null, null],
            ['urug_kochat', 'Yashil Niholzor', '+998 93 345 67 89', 'Jizzax sh., Bog\'bon ko\'chasi 3', null, "Pomidor, bodiring va qalampir ko'chatlari"],
            ['urug_kochat', "Agro Urug' Markazi", '+998 94 456 78 90', 'Toshkent sh., Chilonzor', null, null],
            ['dori', 'AgroHimoya Plus', '+998 95 567 89 01', 'Jizzax sh., Sanoat ko\'chasi 22', null, 'Fungitsid va pestitsidlar'],
            ['svet', "Jizzaxenergo (hudud bo'limi)", '+998 71 123 45 67', 'Jizzax viloyati energo ta\'minoti', '0210 4500 1234 5678', "Abonent raqami to'lov kvitansiyasida"],
            ['gaz', "Hududgazta'minot Jizzax", '+998 71 234 56 78', 'Jizzax shahar gaz xo\'jaligi', '1145 0098 7654 3210', "Abonent raqami to'lov kvitansiyasida"],
            ['suv', 'Jizzaxsuvtaminot', '+998 71 345 67 89', 'Jizzax shahar suv xo\'jaligi', '0033 2211 0099 8877', "Abonent raqami to'lov kvitansiyasida"],
        ];

        foreach ($rows as [$category, $name, $phone, $location, $accountNumber, $note]) {
            Supplier::create([
                'category' => $category,
                'name' => $name,
                'phone' => $phone,
                'location' => $location,
                'account_number' => $accountNumber,
                'note' => $note,
            ]);
        }
    }
}
