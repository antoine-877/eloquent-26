<div>
    <h1>Liste des séances</h1>

    @if ($showtimes->isEmpty())
        <p>Aucune séance</p>
    @else
        @foreach ($showtimes as $showtime)
            <div>
                <h2>{{ $showtime->movie->title }}</h2>
                <p>{{ $showtime->starts_at }}</p>
                <p>{{ $showtime->price }} €</p>
                <p>{{ $showtime->room?->name }}</p>
            </div>
        @endforeach
    @endif
</div>
