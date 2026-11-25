import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft } from 'lucide-react';
import {
    ResponsiveContainer,
    LineChart,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
} from 'recharts';

export default function PlantSpot({ plant, observations }) {
    const latest = observations[observations.length - 1];
    const chartData = observations.map((o) => ({
        date: new Date(o.observed_at).toLocaleDateString('uz-UZ', { day: '2-digit', month: '2-digit' }),
        health: o.health_score,
    }));

    return (
        <AppLayout>
            <Head title={plant.public_code} />

            <Link href="/" className="mb-6 inline-flex items-center gap-1.5 text-sm text-neutral-400 hover:text-white">
                <ArrowLeft className="h-4 w-4" /> Xaritaga qaytish
            </Link>

            <header className="mb-6">
                <h1 className="text-2xl font-semibold">{plant.public_code}</h1>
                <p className="text-sm text-neutral-500">
                    {plant.greenhouse_name} · Blok {plant.zone_code} · Ekilgan: {plant.planted_at}
                </p>
            </header>

            <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div className="lg:col-span-1">
                    <img
                        src={latest.image_url}
                        alt={plant.public_code}
                        className="w-full rounded-2xl border border-neutral-800 object-cover"
                    />
                    <div className="mt-4 rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-neutral-400">Holat</span>
                            <span className="rounded-full px-2 py-0.5 text-xs" style={{ backgroundColor: `${latest.category.color}22`, color: latest.category.color }}>
                                {latest.category.name_uz}
                            </span>
                        </div>
                        <div className="mt-3 flex items-center justify-between">
                            <span className="text-sm text-neutral-400">Sog'liq indeksi</span>
                            <span className="text-lg font-semibold">{latest.health_score}/100</span>
                        </div>
                        <div className="mt-3 flex items-center justify-between">
                            <span className="text-sm text-neutral-400">Ishonch</span>
                            <span className="text-sm">{latest.confidence}%</span>
                        </div>
                        <div className="mt-3 flex items-center justify-between">
                            <span className="text-sm text-neutral-400">Aniqlangan obyektlar</span>
                            <span className="text-sm">{latest.detection_count}</span>
                        </div>
                    </div>
                </div>

                <div className="lg:col-span-2 space-y-6">
                    <section className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                        <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                            Sog'liq tarixi
                        </h2>
                        <div className="h-64">
                            <ResponsiveContainer width="100%" height="100%">
                                <LineChart data={chartData}>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#262626" />
                                    <XAxis dataKey="date" stroke="#737373" fontSize={12} />
                                    <YAxis domain={[0, 100]} stroke="#737373" fontSize={12} />
                                    <Tooltip
                                        contentStyle={{ background: '#171717', border: '1px solid #262626', borderRadius: 8 }}
                                    />
                                    <Line type="monotone" dataKey="health" stroke="#22c55e" strokeWidth={2} dot={{ r: 3 }} />
                                </LineChart>
                            </ResponsiveContainer>
                        </div>
                    </section>

                    <section className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                        <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                            Kuzatuvlar jurnali
                        </h2>
                        <div className="divide-y divide-neutral-800">
                            {[...observations].reverse().map((o) => (
                                <div key={o.id} className="flex items-center justify-between py-3 text-sm">
                                    <div>
                                        <div className="text-neutral-200">{o.category.name_uz}</div>
                                        <div className="text-xs text-neutral-500">
                                            {new Date(o.observed_at).toLocaleString('uz-UZ')}
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="font-medium">{o.health_score}/100</div>
                                        <div className="text-xs text-neutral-500">{o.confidence}% ishonch</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </section>
                </div>
            </div>
        </AppLayout>
    );
}
