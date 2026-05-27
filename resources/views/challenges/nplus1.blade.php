@extends('layouts.app')

@section('title', '課題02: N+1')

@section('content')
    <h1>課題02: N+1 問題を解消する</h1>
    <div class="note">
        下のループ内で <code>$order->user->name</code> と <code>$order->items</code> を参照しています。
        Eager Loading していないと、ここでクエリが多発します。Debugbar のクエリ数を確認しましょう。
        <code>docs/02-nplus1.md</code> 参照。
    </div>

    <table>
        <thead><tr><th>注文ID</th><th>注文者</th><th>明細件数</th><th>注文日</th></tr></thead>
        <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->items->count() }}</td>
                <td>{{ $order->ordered_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
