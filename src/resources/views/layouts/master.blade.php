<!doctype html>
<html lang="en">
    <head>
        <title>@yield('title') - {{ config('mustard.meta.title', 'Mustard') }}</title>
        <meta name="rating" content="General" />
        <meta name="robots" content="all" />
        <meta name="description" content="{{ config('mustard.meta.description', 'The open source market platform.') }}" />
        <meta name="keywords" content="{{ !empty($keywords) ? implode(',', $keywords) : '' }}" />
        @include('mustard::fragments.favicons')
        <link href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/normalize.min.css" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/foundation.min.css" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/foundation-datepicker/1.4.0/css/foundation-datepicker.min.css" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/basic.min.css" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet" type="text/css">
        <link href="//cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.1/jquery.timepicker.min.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            header, nav, main, footer {
                margin-bottom: 16px;
            }

            .expand {
                width: 100%;
            }

            i.success {
                color: #368a55;
            }

            i.alert {
                color: #de2d0f;
            }

            .dropzone {
                border: 0 none;
                padding: 0;
                min-height: auto;
            }

            .dropzone .dz-preview .dz-image {
                border-radius: 3px;
            }

            .sub-nav {
                padding: 0.25rem 0;
            }

            .side-nav ul {
                list-style-type: none;
            }

            .side-nav li a {
                padding: 0 0.5rem !important;
            }

            .side-nav > li {
                font-size: 1rem;
            }

            .side-nav > li > ul {
                margin-bottom: 1rem;
            }

            .side-nav span {
                color: #aaa;
            }

            .side-nav li.active > a {
                font-weight: bold !important;
                background: none repeat scroll 0% 0% rgba(0, 0, 0, 0.05);
            }

            .icon-bar.vertical {
                width: 100%;
            }

            .icon-bar .item.title {
                color: rgb(255, 255, 255);
                font-size: 1.2rem;
                padding: 0.5rem 1rem;
            }

            .listings-item {
                margin-bottom: 16px !important;
            }

            td input[type="file"],
            td input[type="checkbox"],
            td input[type="radio"],
            td select,
            td button {
                margin: 0;
            }

            .mosaic .image {
                position: relative;
                overflow: hidden;
            }

            .mosaic .image img {
                display: block;
            }

            .mosaic .image form {
                position: absolute;
                top: 4px;
                right: 4px;
            }

            .mosaic .image div {
                position: absolute;
                color: #cbcbcb;
                text-shadow: 0px 0px 3px #fff;
            }

            .mosaic .image a {
                display: block;
            }

            .mosaic .image .price {
                text-align: right;
                right: 4px;
                bottom: 4px;
            }

            .mosaic .image .time-left {
                left: 4px;
                top: 4px;
            }

            .mosaic.ended .image:after {
                content: "Ended";
                transform: rotate(-45deg);
                font-size: 32px;
                position: absolute;
                top: 24px;
                left: -48px;
                background-color: #555;
                color: #fff;
                padding: 0px 50px;
            }
        </style>
    </head>
    <body>
        @if (Request::is('admin*'))
            @include('mustard::admin.fragments.header')
        @else
            @include('mustard::fragments.header')
        @endif
        @if (isset($errors) && $errors->any())
            @include('mustard::fragments.errors')
        @endif
        @if (isset($status) || $status = session('status'))
            @include('mustard::fragments.status')
        @endif
        <main>
            @yield('content')
        </main>
        @include('mustard::fragments.footer')
        <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.5.2/js/foundation.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/foundation-datepicker/1.4.0/js/foundation-datepicker.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.1/jquery.timepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            @yield('script')
        </script>
        @if (Request::is('pay/*'))
            @include('stripe/embed')
        @endif
        <script type="text/javascript">
            flash_form_data = {{ json_encode((array) Session::get('input')) }};

            $.fn.onFirst = function(name, fn)
            {
                this.on(name, fn);

                this.each(function()
                {
                    var handlers = $._data(this, 'events')[name.split('.')[0]];

                    var handler = handlers.pop();

                    handlers.splice(0, 0, handler);
                });
            };

            $.fn.renameAttr = function(old_attr, new_attr)
            {
                this.attr(new_attr, this.attr(old_attr));

                this.removeAttr(old_attr);
            };

            $(function()
            {
                // Remove required attr on hidden form elements
                $('form').onFirst('submit', function()
                {
                    $(this).find('input, select, textarea').each(function(e)
                    {
                        $('[required]:hidden', e.target).renameAttr('required', 'required-disable');

                        $('[pattern]:hidden', e.target).renameAttr('pattern', 'pattern-disable');

                        $('[required-disable]:visible', e.target).renameAttr('required-disable', 'required');

                        $('[pattern-disable]:hidden', e.target).renameAttr('pattern-disable', 'pattern');
                    });
                });

                $('.open-datepicker').fdatepicker({
                    format: 'yyyy/mm/dd'
                });

                $('.open-timepicker').timepicker({
                    timeFormat: 'H:i'
                });
            });

            $(document).foundation({
                abide: {
                    patterns: {
                        monetary: /^[0-9]+(\.[0-9]{2})?$/,
                        date: /^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/,
                        time: /^[0-9]{2}:[0-9]{2}$/,
                        intrange: /^[0-9]+(-[0-9]+)?$/,
                        account_number: /^[0-9]{4} ?[0-9]{4}$/,
                        sort_code: /^[0-9]{2}-[0-9]{2}-[0-9]{2}$|^[0-9]{6}$/,
                        close_account: /delete my account/
                    }
                },
                dropdown: {
                    is_hover: true
                }
            });
        </script>
    </body>
</html>
