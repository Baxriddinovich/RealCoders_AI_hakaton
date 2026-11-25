import { motion } from 'framer-motion';

const accents = {
    emerald: { text: 'text-emerald-400', glow: 'from-emerald-500/20' },
    amber: { text: 'text-amber-400', glow: 'from-amber-500/20' },
    red: { text: 'text-red-400', glow: 'from-red-500/20' },
    sky: { text: 'text-sky-400', glow: 'from-sky-500/20' },
};

export default function StatCard({ label, value, hint, accent = 'emerald', icon: Icon, index = 0 }) {
    const a = accents[accent] ?? accents.emerald;

    return (
        <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.4, delay: index * 0.06, ease: 'easeOut' }}
            className="group relative overflow-hidden rounded-2xl border border-neutral-800/80 bg-neutral-900/60 p-5 backdrop-blur-sm transition-colors hover:border-neutral-700"
        >
            <div className={`pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gradient-to-br ${a.glow} to-transparent blur-2xl transition-opacity group-hover:opacity-150`} />
            <div className="relative flex items-start justify-between">
                <div>
                    <div className="text-xs font-medium uppercase tracking-wider text-neutral-500">{label}</div>
                    <div className={`mt-2 text-3xl font-bold tabular-nums ${a.text}`}>{value}</div>
                    {hint && <div className="mt-1 text-xs text-neutral-500">{hint}</div>}
                </div>
                {Icon && (
                    <div className={`rounded-xl bg-neutral-800/80 p-2 ${a.text}`}>
                        <Icon className="h-4 w-4" />
                    </div>
                )}
            </div>
        </motion.div>
    );
}
