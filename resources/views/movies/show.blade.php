<div>
    <h1>{{ $movie->title }}</h1>

    <p>Durée : {{ intdiv($movie->duration, 60) }} h {{ $movie->duration % 60 }}</p>

    <p>{{ $movie->synopsis }}</p>

    <h2>Séances</h2>

    @forelse ($showtimes as $showtime)
        <p>
            {{ $showtime->starts_at->format('G \h i') }}
            — {{ $showtime->price }} €
        </p>
    @empty
        <p>Aucune séance</p>
    @endforelse

</div>
