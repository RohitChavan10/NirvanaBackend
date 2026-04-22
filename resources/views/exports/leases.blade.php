<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; font-size: 12px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h2>Leases Export</h2>

<table>
    <thead>
        <tr>
            @foreach($columns as $col)
                <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                @foreach($columns as $col)
                    <td>{{ $row[$col] ?? '' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>