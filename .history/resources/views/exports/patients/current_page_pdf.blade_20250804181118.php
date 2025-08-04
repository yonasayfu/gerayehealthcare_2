@extends('print-layout')

@section('content')
    <x-printable-report
        :title="$document_title ?? 'Patient List (Current View)'"
        :data="$items"
        :columns="$columns"
        :header-info="[]" {{-- You might need to pass actual header info from config or controller --}}
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ $document_title ?? 'Patient List (Current View)' }}</h1>
    <table>
        <thead>
            <tr>
                @foreach ($columns ?? [] as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($items ?? [] as $item)
                <tr>
                    @foreach ($columns ?? [] as $column)
                        <td>
                            @php
                                $value = data_get($item, $column['key']);
                                if (isset($column['transform']) && is_callable($column['transform'])) {
                                    echo $column['transform']($value, $item);
                                } else {
                                    echo $value;
                                }
                            @endphp
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
