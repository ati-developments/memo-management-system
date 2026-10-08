<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.theme-init')
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'MemoFlow')
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f6f2;
            color: #111827;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 242px;
            background: #1d2645;
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }

        .brand {
            height: 88px;
            display: flex;
            align-items: center;
            padding: 0 22px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .brand-logo {
            width: 35px;
            height: 35px;
            background: #2856e8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 13px;
            font-family: Georgia, serif;
            font-size: 18px;
        }

        .brand-name {
            font-family: Georgia, serif;
            font-size: 16px;
            font-weight: bold;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #9da6c2;
            margin-top: 2px;
        }

        .nav {
            padding: 17px 13px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 15px;
            border-radius: 9px;
            margin-bottom: 6px;
            color: #aeb6ce;
            text-decoration: none;
            font-size: 15px;
            transition: .2s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.08);
            color: white;
        }

        .nav-item.active {
            background: #3c4664;
            color: white;
        }

        .nav-item.new-memo {
            background: #2d56e6;
            color: white;
            margin-top: 5px;
            margin-bottom: 13px;
        }

        .nav-icon {
            width: 18px;
            text-align: center;
        }

        .badge {
            margin-left: auto;
            background: #d99108;
            color: white;
            width: 23px;
            height: 23px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .user-area {
            margin-top: auto;
            padding: 20px 22px 14px;
            display: flex;
            align-items: center;
        }

        .logout-form {
            padding: 0 13px 17px;
            border-top: 1px solid rgba(255,255,255,.1);
        }

        .logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 15px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #aeb6ce;
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            text-align: left;
            transition: background .2s, color .2s;
        }

        .logout-button:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .logout-icon {
            width: 18px;
            text-align: center;
            font-size: 17px;
            line-height: 1;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #2d56e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 12px;
        }

        .user-name {
            font-size: 14px;
            font-weight: bold;
        }

        .user-role {
            font-size: 12px;
            color: #9da6c2;
            margin-top: 3px;
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        .main {
            margin-left: 292px;
            width: calc(100% - 272px);
            padding: 20px 54px 40px;
        }

        .content {
            max-width: none;
            margin: 0;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media(max-width: 700px) {

            .sidebar {
                width: 70px;
            }

            .brand-name,
            .brand-subtitle,
            .nav-item span:not(.nav-icon),
            .user-name,
            .user-role,
            .logout-button span:last-child {
                display: none;
            }

            .logout-button {
                justify-content: center;
                padding: 11px;
            }

            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 20px;
            }
        }
    </style>

    @yield('styles')

    @include('layouts.page-header-styles')

</head>

<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<div class="app">

    @include('layouts.sidebar')

    <main class="main" id="main-content" tabindex="-1">
        @include('layouts.workspace-header')

        <div class="content">
            @include('layouts.page-heading')

            @yield('content')

        </div>

    </main>

</div>

@include('layouts.workspace-styles')
@include('layouts.theme-styles')
@include('layouts.workspace-header-styles')

@yield('scripts')
@if(request()->routeIs('memos.my', 'memos.all', 'approvals.index', 'admin.roles.index'))
    <script src="{{ asset('js/memo-live-search.js') }}?v={{ filemtime(public_path('js/memo-live-search.js')) }}" defer></script>
@endif

</body>

</html>
