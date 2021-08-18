<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')@if(request()->path() !== '/') - {{ config('bjyblog.head.title') }} @endif</title>
    <meta name="keywords" content="@yield('keywords')" />
    <meta name="description" content="@yield('description')" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @yield('css')
</head>
<body>
<canvas id='bg' height="1080" width="1920"
        style="position:fixed;z-index:-2;filter: alpha(opacity:50);opacity: 0.9"></canvas>
<header id="b-public-nav" class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            @if(Str::isTrue(config('bjyblog.logo_with_php_tag')))
                <a class="navbar-brand" href="/">
                    <div class="hidden-xs b-nav-background"></div>
                    <ul class="b-logo-code">
                        <li class="b-lc-start">&lt;?php</li>
                        <li class="b-lc-echo">echo</li>
                    </ul>
                    <p class="b-logo-word">'{{ config('app.name') }}'</p>
                    <p class="b-logo-end">;</p>
                </a>
            @else
                <a class="navbar-brand" href="/">
                    <div class="hidden-xs b-nav-background"></div>
                    <p class="b-logo-word">{{ config('app.name') }}</p>
                </a>
            @endif
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav b-nav-parent">
                <li class="hidden-xs b-nav-mobile"></li>
                <li class="b-nav-cname  @if(request()->path() === '/') b-nav-active @endif">
                    <a href="/">{{ translate('Home') }}</a>
                </li>
                @foreach($category as $v)
                    <li class="b-nav-cname @if((request()->fullUrl() === $v->url) || (isset($article) && $article->category_id ===$v->id)) b-nav-active @endif">
                        <a href="{{ $v->url }}">{{ $v->name }}</a>
                    </li>
                @endforeach
                @foreach($nav as $v)
                    <li class="b-nav-cname @if(request()->path() === $v->url) b-nav-active @endif">
                        <a href="{{ url($v->url) }}">{{ $v->name }}</a>
                    </li>
                @endforeach
            </ul>
            <ul id="b-login-word" class="nav navbar-nav navbar-right">
                @if(auth()->guard('socialite')->check())
                    <li class="b-user-info" data-user-id="{{ auth()->guard('socialite')->user()->id }}">
                        <span><img class="b-head_img" src="{{ auth()->guard('socialite')->user()->avatar }}" alt="{{ auth()->guard('socialite')->user()->name }}" title="{{ auth()->guard('socialite')->user()->name }}" /></span>
                        <span class="b-nickname">{{ auth()->guard('socialite')->user()->name }}</span>
                        <span><a href="{{ url('auth/socialite/logout') }}">{{ translate('Sign out') }}</a></span>
                    </li>
                @else
                    <li class="b-nav-cname b-nav-login">
                        <div class="hidden-xs b-login-mobile"></div>
                        <a class="js-login-btn" href="javascript:void(0)">{{ translate('Sign In') }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</header>

<div class="b-h-70"></div>

<div id="b-content" class="container">
    <div class="row">
        @yield('content')
        <div id="b-public-right" class="col-lg-4 hidden-xs hidden-sm hidden-md">
            <div class="b-search">
                <form class="form-inline" role="form" action="{{ url('search') }}" method="get">
                    <input class="b-search-text" type="text" name="wd">
                    <input class="b-search-submit" type="submit" value="{{ translate('Search') }}">
                </form>
            </div>

            <div class="b-qun">
                <dl class="col-xs-10 col-sm-6 col-md-{{ $home_foot_col_number }} col-lg-{{ $home_foot_col_number }}">
                    <dt>{{ translate('Counts') }}</dt>
                    <dd>{{ translate('Article Counts') }}：{{ $article_count }}</dd>
                    <dd>{{ translate('Comment Counts') }}：{{ $comment_count }}</dd>
                    <dd>{{ translate('User Counts') }}：{{ $socialite_user_count }}</dd>
                    <dd>{{ translate('Note Counts') }}：{{ $chat_count }}</dd>
                </dl>
            </div>

            <div class="b-tags">
                <h4 class="b-title">{{ translate('Hot Tags') }}</h4>
                <ul class="b-all-tname">
                    @foreach($tag as $v)
                        <li class="b-tname">
                            <a class="b-tag-style-{{ $loop->index%4 }}" href="{{ $v->url }}">{{ $v->name }} ({{ $v->articles_count }})</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="b-recommend">
                <h4 class="b-title">{{ translate('Top Articles') }}</h4>
                <p class="b-recommend-p">
                    @foreach($top_article as $v)
                        <a class="b-recommend-a" href="{{ $v->url }}" target="{{ config('bjyblog.link_target') }}"><span class="fa fa-th-list b-black"></span> {{ $v->title }}</a>
                    @endforeach
                </p>
            </div>
            <div class="b-comment-list">
                <h4 class="b-title">{{ translate('Recent Comments') }}</h4>
                <div>
                    @foreach($latest_comments as $comment)
                        <ul class="b-new-comment @if($loop->first) b-new-commit-first @endif">
                            <img class="b-head-img bjy-lazyload" src="{{ cdn_url('images/default/avatar.jpg') }}" data-src="{{ cdn_url($comment->socialiteUser->avatar) }}" alt="{{ $comment->socialiteUser->name }}">
                            <li class="b-nickname">
                                {{ $comment->socialiteUser->name }} <i class="fa fa-{{ $comment->socialiteUser->socialiteClient->icon }}"></i> <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </li>
                            <li class="b-nc-article">
                                {{ translate('Comment') }} <a href="{{ $comment->article->url }}#comment-{{ $comment->id }}" target="{{ config('bjyblog.link_target') }}">{{ $comment->article->sub_title }}</a>
                            </li>
                            <li class="b-content">
                                {!! $comment->sub_content !!}
                            </li>
                        </ul>
                    @endforeach
                </div>
            </div>
            <div class="b-link">
                <h4 class="b-title">{{ translate('Links') }}</h4>
                <p>
                    @foreach($friend as $v)
                        <a class="b-link-a" href="{{ $v->url }}" target="{{ config('bjyblog.link_target') }}"><span class="fa fa-link b-black"></span> {{ $v->name }}</a>
                    @endforeach
                    <a class="b-link-a" href="{{ url('site') }}"><span class="fa fa-link b-black"></span> {{ translate('More') }} </a>
                </p>
            </div>
        </div>
    </div>
</div>


<footer id="b-foot">
    <div class="container">
        <div class="text-center text-capitalize">

            <span>本博客使用免费开源的</span>
            <span><a href="https://github.com/baijunyao/laravel-bjyblog">laravel-bjyblog</a></span>
           <span>搭建</span>{{(" ")}}|{{(" ")}}<span>© 2021 版权所有</span>
           {{(" ")}}|{{(" ")}}<span>ICP证：</span><a  href="https://beian.miit.gov.cn" target="{{ config('bjyblog.link_target') }}">{{ config('bjyblog.icp') }}</a>
        </div>
    </div>
    <a class="go-top fa fa-angle-up animated jello" href="javascript:void(0)"></a>
</footer>

<div class="modal fade" id="b-modal-login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content row">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title b-ta-center" id="myModalLabel">{{ translate('Without registration, you can log in with the account below.') }}</h4>
                </div>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12 b-login-row">
                @foreach($socialite_clients as $socialiteClient)
                    <a class="fa fa-{{ $socialiteClient->icon }}" href="{{ url('auth/socialite/redirectToProvider/' . $socialiteClient->name) }}" alt="{{ $socialiteClient->name }}" title="{{ $socialiteClient->name }}"></a>
                @endforeach

                @if($socialite_clients->isEmpty())
                    {{ translate('Need to add socialite client first.') }} <a href="{{ url('admin/socialiteClient/index') }}">{{ translate('Click to go') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
<script>

    ajaxCommentUrl = "{{ url('comment') }}";
    ajaxLikeUrl = "{{ url('like/store') }}";
    ajaxUnLikeUrl = "{{ url('like/destroy') }}";
    socialiteUserShowUrl = "{{ url('socialiteUser/me') }}";
    titleName = '{{ config('app.name') }}';
    jsSocialsConfig = {!! config('bjyblog.social_share.jssocials_config') !!};
    sharejsConfig = {!! config('bjyblog.social_share.sharejs_config') !!};
    pleaseLogInFirst = '{{ translate('Please log in first.') }}';
    submittedSuccessfullyWaitingForApprove = '{{ translate('Submitted successfully, waiting for approve.') }}';
</script>
<script src="{{ mix('js/app.js') }}"></script>
{!! htmlspecialchars_decode(config('bjyblog.statistics')) !!}
@yield('js')
</body>
</html>
