<div>
    <h1>Liste des films</h1>

    @if ($movies->isEmpty())
        <p>Aucun film</p>
    @else
        <table>
            <tr>
                <th>Titre</th>
                <th>Durée</th>
                <th>Synopsis</th>
                <th>Genres</th>
            </tr>

            @foreach ($movies as $movie)
                <tr>
                    <td>{{ $movie->title }}</td>
                    <td>{{ intdiv($movie->duration, 60) }} h {{ $movie->duration % 60 }}</td>
                    <td>{{ $movie->synopsis }}</td>
                    <td>
                        @foreach ($movie->genres as $genre)
                            {{ $genre->name }},
                        @endforeach
                    </td>

                </tr>
            @endforeach
        </table>
    @endif



</div>
