@extends('layouts.app')

@section('title', '課題06: ページネーション')

@section('content')
    <h1>課題06: 大量データのページネーション</h1>
    <div class="note">
        URL に <code>?page=1</code> と <code>?page=4000</code> を付けて、Debugbar の実行時間を比較しましょう。
        OFFSET 方式は深いページで遅くなります。<code>docs/06-pagination.md</code> 参照。
    </div>

    <table>
        <thead><tr><th>注文ID</th><th>ユーザーID</th><th>ステータス</th><th>注文日</th></tr></thead>
        <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user_id }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->ordered_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="margin-top:16px;">
        {{ $orders->links() }}
    </div>
@endsection
