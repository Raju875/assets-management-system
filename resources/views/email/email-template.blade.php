<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>@yield('title')</title>
    <link href='https://fonts.googleapis.com/css?family=Vollkorn' rel='stylesheet' type='text/css'>

<style>
    body {
        background: #e7e9ed;
        color: #535b61;
        font-family: "Poppins", sans-serif;
        font-size: 14px;
        line-height: 22px;
    }
</style>
</head>

<body>
<table width="80%" style="background-color:#ffffff;margin:0 auto; height:50px; border-radius: 4px;font-size: 14px; font-family: Vollkorn">
    <thead>
    <tr>
        <td style="padding: 10px; border-bottom: 1px solid rgba(0,0,0,.1);">
            <h4 style="text-align:center">
                <img id="logo" src="{{ asset('assets/img/logo.png') }}" title="{{ config('app.name') }}" alt="{{ config('app.name') }}" />
            </h4>
        </td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="margin-top: 20px; padding: 15px;">
            @yield('content')
            <br/><br/>
        </td>
    </tr>
    <tr style="margin-top: 15px;">
        <td style="padding: 1px; border-top: 1px solid rgba(0,0,0,.1);">
            <h5 style="text-align:center">All right reserved by {{ config('app.name') }} {{ date('Y') }}.</h5>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
