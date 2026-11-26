import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { Wallet, Plus, Trash2, X } from 'lucide-react';

const COLORS = ['#22c55e', '#0ea5e9', '#f59e0b', '#a855f7', '#ef4444', '#14b8a6', '#eab308', '#6366f1', '#737373'];

function formatSom(value) {
    return new Intl.NumberFormat('uz-UZ').format(Math.round(value ?? 0)) + " so'm";
}

export default function Expenses({ greenhouses, greenhouseId, categories, expenses, stats, byCategory, byMonth }) {
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        greenhouse_id: greenhouseId,
        category: Object.keys(categories)[0],
        amount: '',
        spent_on: new Date().toISOString().slice(0, 10),
        note: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/expenses', {
            preserveScroll: true,
            onSuccess: () => {
                reset('amount', 'note');
                setShowForm(false);
            },
        });
    };

    const destroy = (id) => {
        if (!confirm("Bu xarajatni o'chirishni tasdiqlaysizmi?")) return;
        router.delete(`/expenses/${id}`, { preserveScroll: true });
    };

    return (
        <AppLayout>
            <Head title="Xarajatlar" />

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-2xl font-semibold">Xarajatlar</h1>
                    <p className="mt-1 text-sm text-neutral-400">Gaz, svet, suv va boshqa issiqxona xarajatlarini kuzatish</p>
                </div>
                <button
                    onClick={() => setShowForm(true)}
                    className="flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500"
                >
                    <Plus className="h-4 w-4" /> Xarajat qo'shish
                </button>
            </div>

            {greenhouses?.length > 1 && (
                <div className="mb-6 flex gap-2 overflow-x-auto pb-1">
                    {greenhouses.map((gh) => (
                        <Link
                            key={gh.id}
                            href={`/expenses?greenhouse=${gh.id}`}
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

            <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard label="Jami xarajat" value={formatSom(stats.total_all)} icon={Wallet} />
                <StatCard label="Shu oy" value={formatSom(stats.total_this_month)} />
                <StatCard label="1 kg hosil tannarxi" value={stats.cost_per_kg ? formatSom(stats.cost_per_kg) : '—'} />
                <StatCard label="1 gektar uchun" value={stats.cost_per_ha ? formatSom(stats.cost_per_ha) : '—'} />
            </div>

            <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                    <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        Toifalar bo'yicha taqsimot
                    </h2>
                    {byCategory.length > 0 ? (
                        <div className="h-72">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie
                                        data={byCategory}
                                        dataKey="total"
                                        nameKey="label"
                                        innerRadius={60}
                                        outerRadius={100}
                                        paddingAngle={2}
                                    >
                                        {byCategory.map((entry, i) => (
                                            <Cell key={entry.category} fill={COLORS[i % COLORS.length]} />
                                        ))}
                                    </Pie>
                                    <Tooltip
                                        formatter={(value) => formatSom(value)}
                                        contentStyle={{ background: '#171717', border: '1px solid #262626', borderRadius: 8 }}
                                    />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                    ) : (
                        <p className="py-10 text-center text-sm text-neutral-500">Hali xarajat kiritilmagan</p>
                    )}
                    <div className="mt-2 divide-y divide-neutral-800 text-sm">
                        {byCategory.map((c, i) => (
                            <Link
                                key={c.category}
                                href={`/expenses/toifa/${c.category}`}
                                className="flex items-center justify-between py-2 transition hover:text-emerald-400"
                            >
                                <span className="flex items-center gap-2 text-neutral-300 group-hover:text-emerald-400">
                                    <span className="h-2.5 w-2.5 rounded-full" style={{ background: COLORS[i % COLORS.length] }} />
                                    {c.label}
                                </span>
                                <span className="font-medium text-neutral-100">{formatSom(c.total)}</span>
                            </Link>
                        ))}
                    </div>
                </section>

                <section className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                    <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">
                        Oylar bo'yicha dinamika
                    </h2>
                    <div className="h-72">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={byMonth}>
                                <CartesianGrid strokeDasharray="3 3" stroke="#262626" />
                                <XAxis dataKey="month" stroke="#737373" fontSize={12} />
                                <YAxis stroke="#737373" fontSize={12} />
                                <Tooltip
                                    formatter={(value) => formatSom(value)}
                                    contentStyle={{ background: '#171717', border: '1px solid #262626', borderRadius: 8 }}
                                />
                                <Bar dataKey="total" fill="#0ea5e9" radius={[4, 4, 0, 0]} />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </section>
            </div>

            <section className="mt-6 rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-neutral-400">Barcha yozuvlar</h2>
                <div className="divide-y divide-neutral-800 text-sm">
                    {expenses.length === 0 && <p className="py-6 text-center text-neutral-500">Hali xarajat kiritilmagan</p>}
                    {expenses.map((e) => (
                        <div key={e.id} className="flex items-center justify-between gap-3 py-2.5">
                            <div>
                                <Link
                                    href={`/expenses/toifa/${e.category}`}
                                    className="font-medium text-neutral-100 hover:text-emerald-400"
                                >
                                    {categories[e.category] ?? e.category}
                                </Link>
                                <div className="text-xs text-neutral-500">
                                    {new Date(e.spent_on).toLocaleDateString('uz-UZ')} {e.note ? `· ${e.note}` : ''}
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className="font-semibold text-neutral-100">{formatSom(e.amount)}</span>
                                <button onClick={() => destroy(e.id)} className="text-neutral-500 hover:text-red-400">
                                    <Trash2 className="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            {showForm && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                    <form
                        onSubmit={submit}
                        className="w-full max-w-md rounded-2xl border border-neutral-800 bg-neutral-900 p-6"
                    >
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-semibold">Yangi xarajat</h3>
                            <button type="button" onClick={() => setShowForm(false)} className="text-neutral-500 hover:text-white">
                                <X className="h-5 w-5" />
                            </button>
                        </div>

                        <label className="mb-1 block text-xs text-neutral-400">Toifa</label>
                        <select
                            value={data.category}
                            onChange={(e) => setData('category', e.target.value)}
                            className="mb-3 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-100"
                        >
                            {Object.entries(categories).map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>

                        <label className="mb-1 block text-xs text-neutral-400">Summa (so'm)</label>
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            value={data.amount}
                            onChange={(e) => setData('amount', e.target.value)}
                            className="mb-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-100"
                            required
                        />
                        {errors.amount && <p className="mb-2 text-xs text-red-400">{errors.amount}</p>}

                        <label className="mb-1 block text-xs text-neutral-400">Sana</label>
                        <input
                            type="date"
                            value={data.spent_on}
                            onChange={(e) => setData('spent_on', e.target.value)}
                            className="mb-3 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-100"
                            required
                        />

                        <label className="mb-1 block text-xs text-neutral-400">Izoh (ixtiyoriy)</label>
                        <input
                            type="text"
                            value={data.note}
                            onChange={(e) => setData('note', e.target.value)}
                            className="mb-4 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-100"
                        />

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500 disabled:opacity-40"
                        >
                            Saqlash
                        </button>
                    </form>
                </div>
            )}
        </AppLayout>
    );
}

function StatCard({ label, value, icon: Icon }) {
    return (
        <div className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
            <div className="flex items-center justify-between">
                <span className="text-xs uppercase tracking-wide text-neutral-500">{label}</span>
                {Icon && <Icon className="h-4 w-4 text-neutral-500" />}
            </div>
            <div className="mt-2 text-xl font-bold text-neutral-100">{value}</div>
        </div>
    );
}
