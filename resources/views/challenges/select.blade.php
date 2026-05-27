@extends('layouts.app')

@section('title', '課題01: SELECT *')

@section('content')
    <h1>課題01: 必要なカラムだけ取得する</h1>
    <div class="note">
        表示しているのは <strong>名前と価格だけ</strong>です。それなのに全カラムを取得していないか、
        Debugbar の実行時間とともに確認しましょう。<code>docs/01-select.md</code> 参照。
    </div>

    <table>
        <thead><tr><th>ID</th><th>商品名</th><th>価格</th></tr></thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price) }} 円</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
