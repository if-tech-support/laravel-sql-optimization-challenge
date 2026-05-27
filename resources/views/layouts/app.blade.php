<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SQL最適化チャレンジ')</title>
    <style>
        body { font-family: -apple-system, "Segoe UI", "Noto Sans JP", sans-serif; margin: 0; background: #f6fdff; color: #082024; }
        header { background: #1f8599; color: #fff; padding: 16px 24px; }
        header a { color: #cdeef4; text-decoration: none; font-size: 14px; }
        main { max-width: 960px; margin: 0 auto; padding: 24px; }
        h1 { font-size: 22px; }
        .note { background: #fff; border-left: 4px solid #2cb8d1; padding: 12px 16px; margin: 16px 0; border-radius: 4px; }
        table { border-collapse: collapse; width: 100%; background: #fff; margin-top: 12px; }
        th, td { border: 1px solid #d8eef3; padding: 6px 10px; text-align: left; font-size: 14px; }
        th { background: #eaf8fb; }
        ul.menu { list-style: none; padding: 0; }
        ul.menu li { background: #fff; margin: 8px 0; padding: 14px 16px; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,.06); }
        ul.menu a { font-weight: 600; color: #1f8599; text-decoration: none; }
        code { background: #eaf8fb; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('index') }}">← 課題一覧へ</a>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
