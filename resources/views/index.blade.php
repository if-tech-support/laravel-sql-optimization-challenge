@extends('layouts.app')

@section('title', 'SQL最適化チャレンジ')

@section('content')
    <h1>SQL最適化チャレンジ</h1>
    <div class="note">
        各課題には「動くが遅い実装」が用意されています。<br>
        画面下部の <strong>Debugbar</strong>（クエリ数・実行時間）を見ながら、対応する
        <code>docs/</code> の指示に沿って最適化してください。
    </div>

    <ul class="menu">
        <li><a href="{{ route('challenges.select') }}">課題01: 必要なカラムだけ取得する（SELECT * を避ける）</a><br><small>docs/01-select.md</small></li>
        <li><a href="{{ route('challenges.nplus1') }}">課題02: N+1 問題を解消する（Eager Loading）</a><br><small>docs/02-nplus1.md</small></li>
        <li><a href="{{ route('challenges.left-join') }}">課題03: LEFT JOIN で 0 件側を取りこぼさない</a><br><small>docs/03-left-join.md</small></li>
        <li><a href="{{ route('challenges.aggregation') }}">課題04: 集計は PHP ではなく DB にやらせる</a><br><small>docs/04-aggregation.md</small></li>
        <li><a href="{{ route('challenges.indexing') }}">課題05: インデックスを設計する</a><br><small>docs/05-indexing.md</small></li>
        <li><a href="{{ route('challenges.pagination') }}">課題06: 大量データのページネーション（offset と cursor）</a><br><small>docs/06-pagination.md</small></li>
    </ul>
@endsection
