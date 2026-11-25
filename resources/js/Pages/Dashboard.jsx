import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AppLayout from '@/Layouts/AppLayout';
import StatCard from '@/Components/StatCard';
import PlantTile from '@/Components/PlantTile';
import { Sprout, TriangleAlert, HeartPulse, Bot, BatteryMedium, Leaf } from 'lucide-react';

export default function Dashboard({ greenhouses, greenhouse, zones, stats, categoryBreakdown, alerts, forecasts, robot }) {
    const healthyPct = stats.total_plants ? Math.round((stats.healthy_plants / stats.total_plants) * 100) : 0;

    return (
        <AppLayout>
            <Head title="Boshqaruv paneli" />

            {greenhouses.length > 1 && (
                <div className="mb-4 flex gap-2">
                    {greenhouses.map((gh) => (
                        <Link
                            key={gh.id}
                            href={`/?greenhouse=${gh.id}`}
                            preserveState={false}
                            className={`rounded-full border px-4 py-1.5 text-sm transition ${
                                gh.id === greenhouse.id
                                    ? 'border-emerald-700 bg-emerald-950 text-emerald-400'
                                    : 'border-neutral-800 text-neutral-400 hover:border-neutral-700'
                            }`}
                        >
                            {gh.name} <span className="text-xs opacity-60">({gh.crop})</span>
                        </Link>
                    ))}
                </div>
            )}

            {/* Hero */}
            <div className="relative mb-8 overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/60 p-6 md:p-8">
                <div className="pointer-events-none absolute -left-20 -top-24 h-72 w-72 rounded-full bg-emerald-600/20 blur-3xl" />
                <div className="pointer-events-none absolute -right-16 -bottom-24 h-72 w-72 rounded-full bg-sky-600/10 blur-3xl" />
                <div className="relative flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <div className="mb-2 inline-flex items-center gap-2 rounded-full border border-emerald-900/60 bg-emerald-950/40 px-3 py-1 text-xs font-medium text-emerald-400">
                            <Leaf className="h-3.5 w-3.5" /> {greenhouse.crop} · {greenhouse.area_ha} ga
                        </div>
                        <h1 className="text-3xl font-bold tracking-tight text-white md:text-4xl">{greenhouse.name}</h1>
                        <p className="mt-1 text-sm text-neutral-400">{greenhouse.location} · AgroSkan rels-robot AI-skaner tizimi</p>
                    </div>
                    <motion.div
                        animate={{ opacity: [0.6, 1, 0.6] }}
                        transition={{ duration: 2.2, repeat: Infinity, ease: 'easeInOut' }}
                        className="flex items-center gap-2 rounded-full border border-sky-900/60 bg-sky-950/40 px-4 py-2 text-sm text-sky-300"
                    >
                        <span className="h-2 w-2 rounded-full bg-sky-400" />
                        Jonli monitoring faol
                    </motion.div>
                </div>
            </div>

            <section className="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
                <StatCard
                    index={0}
                    icon={HeartPulse}
                    label="O'rtacha sog'liq indeksi"
                    value={`${stats.avg_health}`}
                    hint={`100 balldan · ${stats.total_plants} o'simlik`}
                />
                <StatCard
                    index={1}
                    icon={Sprout}
                    label="Sog'lom o'simliklar"
                    value={`${healthyPct}%`}
                    accent="emerald"
                    hint={`${stats.healthy_plants} / ${stats.total_plants}`}
                />
                <StatCard
                    index={2}
                    icon={TriangleAlert}
                    label="Faol ogohlantirishlar"
                    value={stats.active_alerts}
                    accent="red"
                    hint="Darhol e'tibor talab qiladi"
                />
                <StatCard
                    index={3}
                    icon={Bot}
                    label="Robot holati"
                    value={robot?.status === 'idle' ? 'Tayyor' : robot?.status}
                    accent="sky"
                    hint={robot?.last_scan_at ? `Oxirgi skan: ${new Date(robot.last_scan_at).toLocaleString('uz-UZ')}` : '—'}
                />
            </section>

            <div className="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <motion.section
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.4, delay: 0.2 }}
                    className="rounded-2xl border border-neutral-800 bg-neutral-900/60 p-5 xl:col-span-2"
                >
                    <div className="mb-4 flex items-center justify-between">
                        <h2 className="text-sm font-semibold uppercase tracking-wide text-neutral-400">
                            Issiqxona xaritasi — real skan rasmlari
                        </h2>
                        <span className="text-xs text-neutral-500">{stats.total_plants} ta nuqta</span>
                    </div>
                    <div className="space-y-5">
                        {zones.map((zone) => (
                            <div key={zone.id}>
                                <div className="mb-2 flex items-center gap-2 text-sm font-medium text-neutral-300">
                                    <span className="rounded-md bg-neutral-800 px-2 py-0.5 text-xs">Blok {zone.code}</span>
                                    <span className="text-xs text-neutral-500">{zone.plants.length} o'simlik</span>
                                </div>
                                <div className="flex flex-wrap gap-1">
                                    {zone.plants.map((plant) => (
                                        <PlantTile key={plant.id} plant={plant} />
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="mt-5 flex flex-wrap gap-4 border-t border-neutral-800 pt-4 text-xs text-neutral-500">
                        <LegendDot color="#22c55e" label="Sog'lom (85+)" />
                        <LegendDot color="#eab308" label="Kuzatuvda (65-84)" />
                        <LegendDot color="#f97316" label="Muammo (40-64)" />
                        <LegendDot color="#ef4444" label="Xavfli (<40)" />
                    </div>
                </motion.section>

                <motion.section
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.4, delay: 0.3 }}
                    className="rounded-2xl border border-neutral-800 bg-neutral-900/60 p-5"
                >
                    <h2 className="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        <TriangleAlert className="h-4 w-4" /> So'nggi ogohlantirishlar
                    </h2>
                    <div className="space-y-2.5">
                        {alerts.length === 0 && <p className="text-sm text-neutral-500">Faol ogohlantirish yo'q.</p>}
                        {alerts.map((alert) => (
                            <Link
                                key={alert.id}
                                href={`/plants/${alert.plant_spot_id ?? ''}`}
                                className="group flex items-center gap-3 rounded-xl border border-neutral-800 bg-neutral-950/50 p-2.5 transition hover:border-neutral-700 hover:bg-neutral-950"
                            >
                                <img
                                    src={alert.image_url}
                                    alt={alert.plant_code}
                                    className="h-11 w-11 shrink-0 rounded-lg object-cover"
                                />
                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center justify-between gap-2">
                                        <span className="truncate text-sm font-medium text-neutral-200">{alert.category}</span>
                                        <SeverityBadge severity={alert.severity} />
                                    </div>
                                    <div className="truncate text-xs text-neutral-500">{alert.plant_code}</div>
                                </div>
                            </Link>
                        ))}
                    </div>
                    <Link href="/alerts" className="mt-4 inline-block text-xs font-medium text-emerald-400 hover:underline">
                        Barcha ogohlantirishlarni ko'rish →
                    </Link>
                </motion.section>
            </div>

            <div className="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <motion.section
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.4, delay: 0.4 }}
                    className="rounded-2xl border border-neutral-800 bg-neutral-900/60 p-5"
                >
                    <h2 className="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        <Sprout className="h-4 w-4" /> Hosil prognozi
                    </h2>
                    <div className="grid grid-cols-2 gap-4">
                        {forecasts.map((f, i) => (
                            <motion.div
                                key={f.horizon_days}
                                initial={{ opacity: 0, scale: 0.95 }}
                                animate={{ opacity: 1, scale: 1 }}
                                transition={{ duration: 0.3, delay: 0.5 + i * 0.1 }}
                                className="rounded-xl border border-neutral-800 bg-neutral-950/60 p-4"
                            >
                                <div className="text-xs text-neutral-500">{f.horizon_days} kunlik</div>
                                <div className="mt-1 text-2xl font-bold text-emerald-400">{f.expected_kg} kg</div>
                                <div className="mt-3 flex h-1.5 overflow-hidden rounded-full bg-neutral-800">
                                    <div className="bg-emerald-500" style={{ width: `${f.grade_a_pct}%` }} />
                                    <div className="bg-amber-500" style={{ width: `${f.grade_b_pct}%` }} />
                                    <div className="bg-neutral-600" style={{ width: `${f.grade_c_pct}%` }} />
                                </div>
                                <div className="mt-2 text-xs text-neutral-500">
                                    A: {f.grade_a_pct}% · B: {f.grade_b_pct}% · C: {f.grade_c_pct}%
                                </div>
                            </motion.div>
                        ))}
                    </div>
                    <div className="mt-4 flex items-center gap-2 rounded-xl border border-neutral-800 bg-neutral-950/40 p-3 text-xs text-neutral-500">
                        <BatteryMedium className="h-4 w-4 text-amber-400" />
                        Robot batareyasi: <span className="font-semibold text-neutral-300">{robot?.battery_pct ?? '—'}%</span>
                    </div>
                </motion.section>

                <motion.section
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.4, delay: 0.5 }}
                    className="rounded-2xl border border-neutral-800 bg-neutral-900/60 p-5"
                >
                    <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        Holatlar taqsimoti (so'nggi skan)
                    </h2>
                    <div className="space-y-3">
                        {categoryBreakdown.map((c, i) => {
                            const pct = Math.round((c.total / stats.total_plants) * 100);
                            return (
                                <div key={c.name_uz}>
                                    <div className="mb-1 flex items-center justify-between text-sm">
                                        <span className="flex items-center gap-2 text-neutral-300">
                                            <span className="h-2 w-2 rounded-full" style={{ backgroundColor: c.color }} />
                                            {c.name_uz}
                                        </span>
                                        <span className="text-neutral-500">{c.total}</span>
                                    </div>
                                    <div className="h-1.5 w-full overflow-hidden rounded-full bg-neutral-800">
                                        <motion.div
                                            initial={{ width: 0 }}
                                            animate={{ width: `${pct}%` }}
                                            transition={{ duration: 0.6, delay: 0.5 + i * 0.05, ease: 'easeOut' }}
                                            className="h-full rounded-full"
                                            style={{ backgroundColor: c.color }}
                                        />
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </motion.section>
            </div>
        </AppLayout>
    );
}

function LegendDot({ color, label }) {
    return (
        <span className="flex items-center gap-1.5">
            <span className="h-2 w-2 rounded-full" style={{ backgroundColor: color }} />
            {label}
        </span>
    );
}

function SeverityBadge({ severity }) {
    const styles = {
        high: 'bg-red-950 text-red-400 border-red-900',
        medium: 'bg-orange-950 text-orange-400 border-orange-900',
        low: 'bg-yellow-950 text-yellow-400 border-yellow-900',
    };
    const labels = { high: 'Yuqori', medium: "O'rta", low: 'Past' };

    return (
        <span className={`shrink-0 rounded-full border px-2 py-0.5 text-[10px] uppercase ${styles[severity]}`}>
            {labels[severity]}
        </span>
    );
}
