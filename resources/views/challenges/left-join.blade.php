@extends('layouts.app')

@section('title', '課題03: LEFT JOIN')

@section('content')
    <h1>課題03: LEFT JOIN で 0 件側を取りこぼさない</h1>
    <div class="note">
        レビュー件数を商品ごとに数えています。1 クエリにまとめる際、INNER JOIN にすると
        <strong>レビュー 0 件の商品が消える</strong>点に注意してください。<code>docs/03-left-join.md</code> 参照。
    </div>

    <table>
        <thead><tr><th>商品名</th><th>レビュー件数</th></tr></thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['reviews_count'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
