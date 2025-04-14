<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <title>{{ $title }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #333;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th {
                background-color: #f5f5f5;
                font-weight: bold;
                text-align: left;
                padding: 8px;
                border-bottom: 2px solid #ddd;
            }

            td {
                padding: 8px;
                border-bottom: 1px solid #ddd;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 10px;
                color: #777;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>{{ $title }}</h1>
            <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Generated using Laravel Simple Datatables</p>
        </div>
    </body>
</html>
