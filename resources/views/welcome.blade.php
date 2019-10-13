<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
            <h2><img id="logo" class="logo" alt="CosmoMonger Logo" src="{{ asset('images/Logo.png') }}" />Welcome to CosmoMonger</h2>
            <p>
                CosmoMonger is an on-line space-based trading game featuring real-time
                multi-player interaction.&nbsp; Players own a starship which they use to visit
                planetary systems.&nbsp; Once in a system, players can buy and sell commodities
                (both legal and illegal) to earn credits.&nbsp; Gaining credits allows players
                to buy bigger and more powerful ships.&nbsp; But danger exists side-by-side with
                free enterprise!&nbsp; The Galactic Police are sworn to stop the influx of
                contraband, by any means necessary!&nbsp; Vicious pirates seek to part honest
                merchants from their wares! And most dangerous of all...other players seek fame
                and profit by attacking their competitors!
            </p>
            <p>
                All this for the low, low price of......absolutely nothing!&nbsp; CosmoMonger is
                free to play and worth every darn penny!&nbsp;
            </p>
            <p>
                The path to fame and fortune awaits YOU!&nbsp; Register today and start down the
                path to achieving galatic domination....
            </p>
            </div>
        </div>
    </body>
</html>
