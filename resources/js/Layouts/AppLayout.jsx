import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { LayoutGrid, Bell, BarChart3, Sprout, ScanEye, Menu, X, Wallet } from 'lucide-react';

const nav = [
    { href: '/', label: 'Boshqaruv paneli', icon: LayoutGrid },
    { href: '/ai-tahlil', label: 'AI Tahlil', icon: ScanEye },
    { href: '/alerts', label: 'Ogohlantirishlar', icon: Bell },
    { href: '/reports', label: 'Hisobotlar', icon: BarChart3 },
    { href: '/expenses', label: 'Xarajatlar', icon: Wallet },
];

function Logo() {
    return (
        <div className="flex items-center gap-2.5">
            <div className="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg shadow-emerald-900/40">
                <Sprout className="h-5 w-5 text-white" />
            </div>
            <div>
                <div className="text-lg font-bold leading-none tracking-tight">AgroSkan</div>
                <div className="text-[10px] uppercase tracking-wider text-neutral-500">AI greenhouse scanner</div>
            </div>
        </div>
    );
}

function NavLinks({ url, onNavigate }) {
    return (
        <nav className="space-y-1">
            {nav.map(({ href, label, icon: Icon }) => {
                const active = href === '/' ? url === '/' : url.startsWith(href);
                return (
                    <Link
                        key={href}
                        href={href}
                        onClick={onNavigate}
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
    );
}

export default function AppLayout({ children }) {
    const { url } = usePage();
    const [mobileNavOpen, setMobileNavOpen] = useState(false);

    return (
        <div className="min-h-screen bg-neutral-950 text-neutral-100">
            {/* Mobile top bar */}
            <div className="flex items-center justify-between border-b border-neutral-800 bg-neutral-900/50 p-4 md:hidden">
                <Logo />
                <button
                    onClick={() => setMobileNavOpen(true)}
                    aria-label="Menyuni ochish"
                    className="flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-800 text-neutral-300"
                >
                    <Menu className="h-5 w-5" />
                </button>
            </div>

            {/* Mobile nav drawer */}
            <AnimatePresence>
                {mobileNavOpen && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setMobileNavOpen(false)}
                            className="fixed inset-0 z-40 bg-black/60 md:hidden"
                        />
                        <motion.aside
                            initial={{ x: '-100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '-100%' }}
                            transition={{ duration: 0.2, ease: 'easeOut' }}
                            className="fixed inset-y-0 left-0 z-50 w-72 bg-neutral-900 p-5 md:hidden"
                        >
                            <div className="mb-8 flex items-center justify-between">
                                <Logo />
                                <button
                                    onClick={() => setMobileNavOpen(false)}
                                    aria-label="Menyuni yopish"
                                    className="flex h-8 w-8 items-center justify-center rounded-lg border border-neutral-800 text-neutral-400"
                                >
                                    <X className="h-4 w-4" />
                                </button>
                            </div>
                            <NavLinks url={url} onNavigate={() => setMobileNavOpen(false)} />
                        </motion.aside>
                    </>
                )}
            </AnimatePresence>

            <div className="flex">
                <aside className="hidden w-60 shrink-0 border-r border-neutral-800 bg-neutral-900/50 p-5 md:block">
                    <div className="mb-8">
                        <Logo />
                    </div>
                    <NavLinks url={url} />
                    <div className="mt-10 rounded-lg border border-neutral-800 bg-neutral-900 p-3 text-xs text-neutral-500">
                        Demo dashboard — investor va hamkorlar uchun. Issiqxona xaritasi qulupnay datasetida, AI Tahlil qulupnay + pomidor datasetlarida o'qitilgan.
                    </div>
                </aside>
                <main className="min-w-0 flex-1 p-4 md:p-8">{children}</main>
            </div>
        </div>
    );
}
