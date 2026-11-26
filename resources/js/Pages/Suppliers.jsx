import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft, Phone, MapPin, User } from 'lucide-react';

export default function Suppliers({ category, categoryLabel, suppliers }) {
    return (
        <AppLayout>
            <Head title={`${categoryLabel} — Yetkazib beruvchilar`} />

            <Link
                href="/expenses"
                className="mb-6 inline-flex items-center gap-2 text-sm text-neutral-400 transition hover:text-white"
            >
                <ArrowLeft className="h-4 w-4" /> Xarajatlarga qaytish
            </Link>

            <h1 className="mb-1 text-2xl font-semibold">{categoryLabel}</h1>
            <p className="mb-6 text-sm text-neutral-400">Shu toifa uchun yetkazib beruvchilar/ta'minotchilar ro'yxati</p>

            {suppliers.length === 0 ? (
                <div className="rounded-2xl border border-neutral-800 bg-neutral-900 p-8 text-center text-sm text-neutral-500">
                    Bu toifa uchun hali yetkazib beruvchi kiritilmagan. Filament admin paneldan ("Yetkazib beruvchilar")
                    qo'shishingiz mumkin.
                </div>
            ) : (
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {suppliers.map((s) => (
                        <div key={s.id} className="rounded-2xl border border-neutral-800 bg-neutral-900 p-5">
                            <div className="mb-3 flex items-center gap-2">
                                <User className="h-4 w-4 text-emerald-400" />
                                <span className="font-semibold text-neutral-100">{s.name}</span>
                            </div>
                            {s.phone && (
                                <a
                                    href={`tel:${s.phone}`}
                                    className="mb-1.5 flex items-center gap-2 text-sm text-neutral-300 hover:text-emerald-400"
                                >
                                    <Phone className="h-3.5 w-3.5 text-neutral-500" /> {s.phone}
                                </a>
                            )}
                            {s.location && (
                                <div className="flex items-center gap-2 text-sm text-neutral-300">
                                    <MapPin className="h-3.5 w-3.5 text-neutral-500" /> {s.location}
                                </div>
                            )}
                            {s.note && <p className="mt-2 text-xs text-neutral-500">{s.note}</p>}
                        </div>
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
