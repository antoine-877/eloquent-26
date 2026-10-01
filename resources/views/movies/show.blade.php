<div>
    <h1>{{ $movie->title }}</h1>

    <p>{{ intdiv($movie->duration, 60) }} h {{ $movie->duration % 60 }}</p>


    <p>{{ $movie->synopsis }}</p>


</div>
