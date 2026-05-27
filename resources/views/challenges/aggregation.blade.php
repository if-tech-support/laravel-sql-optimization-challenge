@extends('layouts.app')

@section('title', '課題04: 集計')

@section('content')
    <h1>課題04: 集計は DB にやらせる</h1>
    <div class="note">
        商品ごとの売上上位 20 件です。商品を 1 件ずつループして集計クエリを発行していないか、
        Debugbar のクエリ数で確認しましょう。<code>docs/04-aggregation.md</code> 参照。
    </div>

    <table>
        <thead><tr><th>順位</th><th>商品名</th><th>売上合計</th></tr></thead>
        <tbody>
        @foreach ($report as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ number_format($row['sales']) }} 円</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
