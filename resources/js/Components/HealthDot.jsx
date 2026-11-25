function healthColor(score) {
    if (score === null || score === undefined) return '#404040';
    if (score >= 85) return '#22c55e';
    if (score >= 65) return '#eab308';
    if (score >= 40) return '#f97316';
    return '#ef4444';
}

export default function HealthDot({ score, size = 10 }) {
    return (
        <span
            className="inline-block rounded-full ring-1 ring-black/20"
            style={{ width: size, height: size, backgroundColor: healthColor(score) }}
            title={score !== null ? `Sog'liq: ${score}` : 'Maʻlumot yoʻq'}
        />
    );
}

export { healthColor };
