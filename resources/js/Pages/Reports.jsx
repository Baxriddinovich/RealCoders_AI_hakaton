import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import { FileDown } from 'lucide-react';

export default function Reports({ byZone, byCategory, greenhouseId, greenhouses }) {
    return (
        <AppLayout>
            <Head title="Hisobotlar" />

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold">Hisobotlar</h1>
                {greenhouseId && (
                    <a
                        href={`/greenhouses/${greenhouseId}/report.pdf`}
                        className="flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500"
                    >
                        <FileDown className="h-4 w-4" /> PDF hisobotni yuklash
                    </a>
                )}
            </div>

            {greenhouses?.length > 1 && (
                <div className="mb-6 flex gap-2 overflow-x-auto pb-1">
                    {greenhouses.map((gh) => (
                        <Link
                            key={gh.id}
                            href={`/reports?greenhouse=${gh.id}`}
                            preserveState={false}
                            className={`shrink-0 whitespace-nowrap rounded-full border px-4 py-1.5 text-sm transition ${
                                gh.id === greenhouseId
                                    ? 'border-emerald-700 bg-emerald-950 text-emerald-400'
                                    : 'border-neutral-800 text-neutral-400 hover:border-neutral-700'
                            }`}
                        >
                            {gh.name} <span className="text-xs opacity-60">({gh.crop})</span>
                        </Link>
                    ))}
                </div>
            )}

            <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                    <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        Zonalar bo'yicha o'rtacha sog'liq
                    </h2>
                    <div className="h-72">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={byZone}>
                                <CartesianGrid strokeDasharray="3 3" stroke="#262626" />
                                <XAxis dataKey="zone_code" stroke="#737373" fontSize={12} />
                                <YAxis domain={[0, 100]} stroke="#737373" fontSize={12} />
                                <Tooltip contentStyle={{ background: '#171717', border: '1px solid #262626', borderRadius: 8 }} />
                                <Bar dataKey="avg_health" fill="#22c55e" radius={[4, 4, 0, 0]} />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                    <div className="mt-4 divide-y divide-neutral-800 text-sm">
                        {byZone.map((z) => (
                            <div key={z.zone_code} className="flex items-center justify-between py-2">
                                <span>Blok {z.zone_code}</span>
                                <span className="text-neutral-500">{z.plant_count} o'simlik</span>
                                <span className="text-amber-400">{z.problem_count} muammoli</span>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                    <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        Holatlar bo'yicha taqsimot
                    </h2>
                    <div className="space-y-2">
                        {byCategory.map((c) => (
                            <div key={c.name_uz} className="flex items-center gap-3 text-sm">
                                <span className="h-2.5 w-2.5 shrink-0 rounded-full" style={{ backgroundColor: c.color }} />
                                <span className="flex-1 text-neutral-300">{c.name_uz}</span>
                                <span className="text-xs uppercase text-neutral-600">{c.type}</span>
                                <span className="w-8 text-right text-neutral-500">{c.total}</span>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
