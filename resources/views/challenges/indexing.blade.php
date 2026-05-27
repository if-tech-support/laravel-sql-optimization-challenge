@extends('layouts.app')

@section('title', '課題05: インデックス')

@section('content')
    <h1>課題05: インデックスを設計する</h1>
    <div class="note">
        下は実行クエリの <code>EXPLAIN</code> 結果です。<code>type</code> 列が <code>ALL</code> なら
        フルスキャンです。インデックスを追加して <code>ref</code> や <code>range</code> に変わるかを確認しましょう。
        <code>docs/05-indexing.md</code> 参照。
    </div>

    <h2>EXPLAIN 結果</h2>
    <table>
        <thead>
            <tr>
                @foreach (($explain->first() ? (array) $explain->first() : []) as $key => $_)
                    <th>{{ $key }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach ($explain as $row)
            <tr>
                @foreach ((array) $row as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>取得結果（先頭50件）</h2>
    <table>
        <thead><tr><th>注文ID</th><th>ステータス</th><th>注文日</th></tr></thead>
        <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->ordered_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
