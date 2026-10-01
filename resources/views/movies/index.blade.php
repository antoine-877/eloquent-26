<div>
    <h1>Liste des films</h1>

<table>
    <tr>
        <th>Titre</th>
        <th>Durée</th>
        <th>Date de sortie</th>
        <th>Synopsis</th>
    </tr>

    @foreach ($movies as $movie)
        <tr>
            <td>{{ $movie->title }}</td>
            <td>{{ $movie->duration }} minutes</td>
            <td>{{ $movie->released_on }}</td>
            <td>{{ $movie->synopsis }}</td>
        </tr>
    @endforeach
</table>


</div>
