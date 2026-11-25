import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid, Bell, BarChart3, Sprout, ScanEye } from 'lucide-react';

const nav = [
    { href: '/', label: 'Boshqaruv paneli', icon: LayoutGrid },
    { href: '/ai-tahlil', label: 'AI Tahlil', icon: ScanEye },
    { href: '/alerts', label: 'Ogohlantirishlar', icon: Bell },
    { href: '/reports', label: 'Hisobotlar', icon: BarChart3 },
];

export default function AppLayout({ children }) {
    const { url } = usePage();

    return (
        <div className="min-h-screen bg-neutral-950 text-neutral-100">
            <div className="flex">
                <aside className="hidden w-60 shrink-0 border-r border-neutral-800 bg-neutral-900/50 p-5 md:block">
                    <div className="mb-8 flex items-center gap-2.5">
                        <div className="relative flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg shadow-emerald-900/40">
                            <Sprout className="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <div className="text-lg font-bold leading-none tracking-tight">AgroSkan</div>
                            <div className="text-[10px] uppercase tracking-wider text-neutral-500">AI greenhouse scanner</div>
                        </div>
                    </div>
                    <nav className="space-y-1">
                        {nav.map(({ href, label, icon: Icon }) => {
                            const active = href === '/' ? url === '/' : url.startsWith(href);
                            return (
                                <Link
                                    key={href}
                                    href={href}
                                    className={`flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition ${
                                        active
                                            ? 'bg-emerald-950/60 text-emerald-400'
                                            : 'text-neutral-300 hover:bg-neutral-800 hover:text-white'
                                    }`}
                                >
                                    <Icon className="h-4 w-4" />
                                    {label}
                                </Link>
                            );
                        })}
                    </nav>
                    <div className="mt-10 rounded-lg border border-neutral-800 bg-neutral-900 p-3 text-xs text-neutral-500">
                        Demo dashboard — investor va hamkorlar uchun. Issiqxona xaritasi qulupnay datasetida, AI Tahlil qulupnay + pomidor datasetlarida o'qitilgan.
                    </div>
                </aside>
                <main className="min-w-0 flex-1 p-5 md:p-8">{children}</main>
            </div>
        </div>
    );
}
