<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1a1a1a; font-size: 11px; }
        h1 { font-size: 20px; margin: 0 0 2px; color: #0f5132; }
        .sub { color: #666; font-size: 11px; margin-bottom: 18px; }
        .stats { width: 100%; margin-bottom: 18px; }
        .stats td { width: 25%; padding: 10px 12px; border: 1px solid #d9d9d9; }
        .stat-label { font-size: 9px; text-transform: uppercase; color: #888; }
        .stat-value { font-size: 18px; font-weight: bold; color: #0f5132; }
        h2 { font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-top: 22px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data th { background: #f2f2f2; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #555; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; color: #fff; }
        .footer { margin-top: 24px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>AgroSkan — Issiqxona hisoboti</h1>
    <div class="sub">{{ $greenhouse->name }} &middot; {{ $greenhouse->location }} &middot; {{ $generatedAt }} holatiga</div>

    <table class="stats">
        <tr>
            <td>
                <div class="stat-label">O'rtacha sog'liq indeksi</div>
                <div class="stat-value">{{ $avgHealth }}/100</div>
            </td>
            <td>
                <div class="stat-label">Jami o'simliklar</div>
                <div class="stat-value">{{ $totalPlants }}</div>
            </td>
            <td>
                <div class="stat-label">Faol ogohlantirishlar</div>
                <div class="stat-value">{{ $activeAlerts }}</div>
            </td>
            <td>
                <div class="stat-label">Ekin</div>
                <div class="stat-value" style="font-size:14px">{{ $greenhouse->crop }}</div>
            </td>
        </tr>
    </table>

    <h2>Hosil prognozi</h2>
    <table class="data">
        <tr><th>Ufq</th><th>Kutilgan hosil</th><th>A nav</th><th>B nav</th><th>C nav</th></tr>
        @foreach ($forecasts as $f)
        <tr>
            <td>{{ $f->horizon_days }} kun</td>
            <td>{{ $f->expected_kg }} kg</td>
            <td>{{ $f->grade_a_pct }}%</td>
            <td>{{ $f->grade_b_pct }}%</td>
            <td>{{ $f->grade_c_pct }}%</td>
        </tr>
        @endforeach
    </table>

    <h2>Bloklar bo'yicha holat</h2>
    <table class="data">
        <tr><th>Blok</th><th>O'simliklar</th><th>O'rtacha sog'liq</th><th>Muammoli o'simliklar</th></tr>
        @foreach ($byZone as $zone)
        <tr>
            <td>{{ $zone['zone_code'] }}</td>
            <td>{{ $zone['plant_count'] }}</td>
            <td>{{ $zone['avg_health'] ?? '—' }}</td>
            <td>{{ $zone['problem_count'] }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Holatlar taqsimoti</h2>
    <table class="data">
        <tr><th>Holat</th><th>Turi</th><th>Soni</th></tr>
        @foreach ($byCategory as $cat)
        <tr>
            <td>{{ $cat->name_uz }}</td>
            <td>{{ $cat->type }}</td>
            <td>{{ $cat->total }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Faol ogohlantirishlar ({{ $alerts->count() }})</h2>
    <table class="data">
        <tr><th>O'simlik</th><th>Holat</th><th>Muhimlik</th><th>Vaqt</th></tr>
        @foreach ($alerts as $alert)
        <tr>
            <td>{{ $alert->observation->plantSpot->public_code }}</td>
            <td>{{ $alert->observation->category->name_uz }}</td>
            <td>{{ ['high' => 'Yuqori', 'medium' => "O'rta", 'low' => 'Past'][$alert->severity] }}</td>
            <td>{{ $alert->created_at->format('d.m.Y H:i') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="footer">AgroSkan AI Greenhouse Scanner &middot; avtomatik generatsiya qilingan hisobot</div>
</body>
</html>
