import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const severityStyles = {
    high: 'bg-red-950 text-red-400 border-red-900',
    medium: 'bg-orange-950 text-orange-400 border-orange-900',
    low: 'bg-yellow-950 text-yellow-400 border-yellow-900',
};
const severityLabels = { high: 'Yuqori', medium: "O'rta", low: 'Past' };

const statusLabels = { new: 'Yangi', acknowledged: 'Tekshirilmoqda', resolved: 'Bajarildi' };

export default function Alerts({ alerts, filters, greenhouses }) {
    const setFilter = (key, value) => {
        const next = { status: filters.status || undefined, greenhouse: filters.greenhouse || undefined, [key]: value || undefined };
        router.get('/alerts', next, { preserveState: true });
    };

    const updateAlert = (id, status) => {
        router.patch(`/alerts/${id}`, { status }, { preserveScroll: true });
    };

    return (
        <AppLayout>
            <Head title="Ogohlantirishlar" />

            <header className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold">Ogohlantirishlar</h1>
                <div className="flex gap-2 text-sm">
                    {[
                        ['', 'Hammasi'],
                        ['new', 'Yangi'],
                        ['acknowledged', 'Tekshirilmoqda'],
                        ['resolved', 'Bajarildi'],
                    ].map(([value, label]) => (
                        <button
                            key={value}
                            onClick={() => setFilter('status', value)}
                            className={`rounded-full border px-3 py-1 ${
                                (filters.status || '') === value
                                    ? 'border-emerald-700 bg-emerald-950 text-emerald-400'
                                    : 'border-neutral-800 text-neutral-400 hover:border-neutral-700'
                            }`}
                        >
                            {label}
                        </button>
                    ))}
                </div>
            </header>

            {greenhouses?.length > 1 && (
                <div className="mb-6 flex gap-2">
                    <button
                        onClick={() => setFilter('greenhouse', '')}
                        className={`rounded-full border px-4 py-1.5 text-sm ${
                            !filters.greenhouse
                                ? 'border-emerald-700 bg-emerald-950 text-emerald-400'
                                : 'border-neutral-800 text-neutral-400 hover:border-neutral-700'
                        }`}
                    >
                        Barcha issiqxonalar
                    </button>
                    {greenhouses.map((gh) => (
                        <button
                            key={gh.id}
                            onClick={() => setFilter('greenhouse', gh.id)}
                            className={`rounded-full border px-4 py-1.5 text-sm ${
                                Number(filters.greenhouse) === gh.id
                                    ? 'border-emerald-700 bg-emerald-950 text-emerald-400'
                                    : 'border-neutral-800 text-neutral-400 hover:border-neutral-700'
                            }`}
                        >
                            {gh.name}
                        </button>
                    ))}
                </div>
            )}

            <div className="space-y-3">
                {alerts.data.map((alert) => (
                    <div key={alert.id} className="flex items-center gap-4 rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                        <img src={alert.image_url} alt={alert.plant_code} className="h-16 w-16 shrink-0 rounded-lg object-cover" />
                        <div className="min-w-0 flex-1">
                            <div className="flex items-center gap-2">
                                <span className="font-medium">{alert.category}</span>
                                <span className={`rounded-full border px-2 py-0.5 text-[10px] uppercase ${severityStyles[alert.severity]}`}>
                                    {severityLabels[alert.severity]}
                                </span>
                            </div>
                            <p className="mt-1 truncate text-sm text-neutral-400">{alert.message}</p>
                            <Link href={`/plants/${alert.plant_spot_id}`} className="text-xs text-emerald-400 hover:underline">
                                {alert.plant_code} · Blok {alert.zone_code} · {alert.greenhouse_name}
                            </Link>
                        </div>
                        <div className="flex shrink-0 items-center gap-2">
                            <select
                                value={alert.status}
                                onChange={(e) => updateAlert(alert.id, e.target.value)}
                                className="rounded-lg border border-neutral-800 bg-neutral-950 px-2 py-1.5 text-xs text-neutral-300"
                            >
                                {Object.entries(statusLabels).map(([value, label]) => (
                                    <option key={value} value={value}>
                                        {label}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                ))}
                {alerts.data.length === 0 && <p className="text-sm text-neutral-500">Hech narsa topilmadi.</p>}
            </div>

            {alerts.links && (
                <div className="mt-6 flex gap-2 text-sm">
                    {alerts.links.map((link, i) => (
                        <button
                            key={i}
                            disabled={!link.url}
                            onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                            className={`rounded px-3 py-1 ${
                                link.active ? 'bg-emerald-700 text-white' : 'text-neutral-400 disabled:opacity-30'
                            }`}
                        />
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
