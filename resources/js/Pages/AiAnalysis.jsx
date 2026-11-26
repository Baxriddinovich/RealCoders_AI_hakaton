import { Head, useForm, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { ScanEye, UploadCloud, CheckCircle2, FlaskConical } from 'lucide-react';

const CROP_LABELS = {
    tomato: 'Pomidor',
    cucumber: 'Bodiring',
    pepper: 'Qalampir',
};

export default function AiAnalysis({ modelInfo }) {
    const { flash } = usePage().props;
    const analysis = flash?.analysis;
    const [preview, setPreview] = useState(null);
    const CROPS = Object.keys(modelInfo.crops).map((value) => ({ value, label: CROP_LABELS[value] ?? value }));
    const { data, setData, post, processing, errors } = useForm({ image: null, crop: CROPS[0]?.value ?? 'tomato' });

    const cropInfo = modelInfo.crops[data.crop];

    const onFileChange = (e) => {
        const file = e.target.files[0];
        if (!file) return;
        setData('image', file);
        setPreview(URL.createObjectURL(file));
    };

    const submit = (e) => {
        e.preventDefault();
        post('/ai-tahlil', { forceFormData: true, preserveScroll: true });
    };

    return (
        <AppLayout>
            <Head title="AI Tahlil" />

            <div className="mb-8">
                <div className="mb-2 inline-flex items-center gap-2 rounded-full border border-sky-900/60 bg-sky-950/40 px-3 py-1 text-xs font-medium text-sky-400">
                    <FlaskConical className="h-3.5 w-3.5" /> Jonli AI modeli — haqiqiy datasetda o'qitilgan
                </div>
                <h1 className="text-2xl font-bold text-white md:text-3xl">AI Tahlil</h1>
                <p className="mt-1 text-sm text-neutral-400">
                    Avval ekin turini tanlang, so'ng barg/meva rasmini yuklang — har bir ekin uchun
                    alohida o'qitilgan model ishlatiladi, javob oldindan tayyorlanmagan.
                </p>
            </div>

            <div className="grid grid-cols-1 gap-6 lg:grid-cols-5">
                <form
                    onSubmit={submit}
                    className="lg:col-span-2 rounded-2xl border border-neutral-800 bg-neutral-900/60 p-6"
                >
                    <div className="mb-4 flex gap-2">
                        {CROPS.map((c) => (
                            <button
                                key={c.value}
                                type="button"
                                onClick={() => setData('crop', c.value)}
                                className={`flex-1 rounded-xl border px-4 py-2.5 text-sm font-semibold transition ${
                                    data.crop === c.value
                                        ? 'border-emerald-700 bg-emerald-950 text-emerald-400'
                                        : 'border-neutral-800 text-neutral-400 hover:border-neutral-700'
                                }`}
                            >
                                {c.label}
                            </button>
                        ))}
                    </div>

                    <label
                        htmlFor="image"
                        className="flex min-h-56 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-neutral-700 bg-neutral-950/40 p-6 text-center transition hover:border-emerald-700"
                    >
                        {preview ? (
                            <img src={preview} alt="preview" className="max-h-48 rounded-lg object-contain" />
                        ) : (
                            <>
                                <UploadCloud className="mb-3 h-10 w-10 text-neutral-500" />
                                <span className="text-sm text-neutral-400">Rasm tanlash uchun bosing</span>
                                <span className="mt-1 text-xs text-neutral-600">JPG/PNG, maksimum 10MB</span>
                            </>
                        )}
                        <input id="image" type="file" accept="image/*" onChange={onFileChange} className="hidden" />
                    </label>
                    {errors.image && <p className="mt-2 text-sm text-red-400">{errors.image}</p>}

                    <button
                        type="submit"
                        disabled={!data.image || processing}
                        className="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <ScanEye className="h-4 w-4" />
                        {processing ? 'Tahlil qilinmoqda...' : `${CROPS.find((c) => c.value === data.crop)?.label} modeli bilan tahlil qilish`}
                    </button>

                    <div className="mt-6 rounded-xl border border-neutral-800 bg-neutral-950/50 p-4 text-xs text-neutral-500">
                        <div className="mb-2 font-medium text-neutral-400">
                            Model haqida (shaffof ma'lumot) — {CROPS.find((c) => c.value === data.crop)?.label}
                        </div>
                        <dl className="space-y-1">
                            <Row label="Algoritm" value={modelInfo.algorithm} />
                            <Row label="O'qitish uchun rasmlar" value={cropInfo.train_size} />
                            <Row label="Test to'plam (ko'rilmagan)" value={cropInfo.test_size} />
                            <Row label="Asosiy toifa aniqligi" value={`${Math.round(modelInfo.coarse_test_accuracy * 100)}%`} />
                            <Row label="Aniq kasallik turi aniqligi" value={`${Math.round(cropInfo.fine_test_accuracy * 100)}%`} />
                            <Row label="O'qitilgan" value={modelInfo.trained_at} />
                        </dl>
                    </div>
                </form>

                <div className="lg:col-span-3">
                    <AnimatePresence mode="wait">
                        {analysis ? (
                            <motion.div
                                key={analysis.image_url}
                                initial={{ opacity: 0, y: 10 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0 }}
                                className="space-y-5"
                            >
                                <div className="rounded-2xl border border-neutral-800 bg-neutral-900/60 p-6">
                                    <div className="flex flex-wrap items-start gap-5">
                                        <img
                                            src={analysis.image_url}
                                            alt="uploaded"
                                            className="h-40 w-40 shrink-0 rounded-xl object-cover"
                                        />
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center gap-2 text-emerald-400">
                                                <CheckCircle2 className="h-5 w-5" />
                                                <span className="text-sm font-medium">
                                                    Tahlil yakunlandi — {CROPS.find((c) => c.value === analysis.crop)?.label}
                                                </span>
                                            </div>
                                            <div className="mt-2 flex flex-wrap items-center gap-2">
                                                <div
                                                    className="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold"
                                                    style={{ backgroundColor: `${analysis.coarse.color}22`, color: analysis.coarse.color }}
                                                >
                                                    {analysis.coarse.label_name}
                                                </div>
                                                <div
                                                    className="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold"
                                                    style={{ backgroundColor: `${analysis.ripeness.color}22`, color: analysis.ripeness.color }}
                                                >
                                                    {analysis.ripeness.label_name}
                                                </div>
                                            </div>
                                            <div className="mt-3 text-3xl font-bold text-white">
                                                {Math.round(analysis.coarse.confidence * 100)}%
                                                <span className="ml-2 text-sm font-normal text-neutral-500">ishonch darajasi</span>
                                            </div>
                                            <p className="mt-2 text-xs text-neutral-500">
                                                Taxminiy aniq tashxis: <span className="text-neutral-300">{analysis.fine.label_name}</span> ({Math.round(analysis.fine.confidence * 100)}%)
                                            </p>
                                            <p className="mt-1 text-xs text-neutral-600">
                                                Pishganlik — rang asosidagi tezkor baho ({Math.round(analysis.ripeness.confidence * 100)}% ishonch), alohida o'qitilgan model emas.
                                            </p>
                                        </div>
                                    </div>

                                    <div className="mt-6">
                                        <div className="mb-3 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                                            Asosiy toifa (yuqori ishonchli)
                                        </div>
                                        <ProbabilityBars items={analysis.coarse.probabilities} />
                                    </div>
                                </div>

                                <div className="rounded-2xl border border-neutral-800 bg-neutral-900/60 p-6">
                                    <div className="mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500">
                                        <span>Aniq kasallik/holat turi</span>
                                    </div>
                                    <ProbabilityBars items={analysis.fine.probabilities} />
                                </div>
                            </motion.div>
                        ) : (
                            <motion.div
                                key="empty"
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                className="flex h-full min-h-80 flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-800 bg-neutral-900/30 p-10 text-center"
                            >
                                <ScanEye className="mb-3 h-10 w-10 text-neutral-700" />
                                <p className="text-sm text-neutral-500">
                                    Natijalarni ko'rish uchun chapdagi formadan ekin turini tanlab, rasm yuklang.
                                </p>
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
            </div>
        </AppLayout>
    );
}

function ProbabilityBars({ items }) {
    return (
        <div className="space-y-2.5">
            {items.map((p, i) => (
                <div key={p.slug}>
                    <div className="mb-1 flex items-center justify-between text-sm">
                        <span className="text-neutral-300">{p.name_uz}</span>
                        <span className="text-neutral-500">{Math.round(p.probability * 100)}%</span>
                    </div>
                    <div className="h-1.5 w-full overflow-hidden rounded-full bg-neutral-800">
                        <motion.div
                            initial={{ width: 0 }}
                            animate={{ width: `${p.probability * 100}%` }}
                            transition={{ duration: 0.5, delay: i * 0.03 }}
                            className="h-full rounded-full"
                            style={{ backgroundColor: p.color }}
                        />
                    </div>
                </div>
            ))}
        </div>
    );
}

function Row({ label, value }) {
    return (
        <div className="flex items-center justify-between">
            <dt>{label}</dt>
            <dd className="font-medium text-neutral-300">{value}</dd>
        </div>
    );
}
