<div>
    <h1>{{ $heading }}</h1>
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column->label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($columns as $column)
                        <td>{{ $row[$column->key] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
