import { Link } from '@inertiajs/react';
import { healthColor } from '@/Components/HealthDot';

export default function PlantTile({ plant, size = 44 }) {
    const color = healthColor(plant.health_score);

    return (
        <Link
            href={`/plants/${plant.id}`}
            title={`${plant.public_code} — ${plant.category_name ?? ''}`}
            className="group relative block shrink-0 overflow-hidden rounded-lg ring-2 transition-transform duration-150 hover:z-10 hover:scale-125"
            style={{ width: size, height: size, ringColor: color }}
        >
            {plant.image_url ? (
                <img
                    src={plant.image_url}
                    alt={plant.public_code}
                    loading="lazy"
                    className="h-full w-full object-cover"
                />
            ) : (
                <div className="h-full w-full bg-neutral-800" />
            )}
            <div
                className="pointer-events-none absolute inset-0 rounded-lg ring-2 ring-inset"
                style={{ boxShadow: `inset 0 0 0 2px ${color}` }}
            />
        </Link>
    );
}
