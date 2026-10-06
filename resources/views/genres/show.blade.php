<div>
    <h1>{{ $genre->name }}</h1>

    @if ($movies->isEmpty())
        <p>Aucun film</p>
    @else
        @foreach ($movies as $movie)
            <div>
                <h2>{{ $movie->title }}</h2>
            </div>
        @endforeach
    @endif
</div>
