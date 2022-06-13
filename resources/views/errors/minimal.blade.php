<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ env('APP_NAME') }}</title>
<style>
    /**/
:root {
  --main-color: #04060c;
  --stroke-color: #d8dee5;
  
}
/**/
body {
  background: var(--main-color);
}
h1 {
  margin: 170px auto 0 auto;
  color: var(--stroke-color);
  font-family: 'Encode Sans Semi Condensed', Verdana, sans-serif;
  font-size: 6rem; line-height: 8rem;
  font-weight: 200;
  text-align: center;
}
h2 {
  margin: 20px auto 30px auto;
  font-family: 'Encode Sans Semi Condensed', Verdana, sans-serif;
  font-size: 1.5rem;
  font-weight: 200;
  text-align: center;
  color: var(--stroke-color);
    letter-spacing: .10rem;
}
h1, h2 {
  -webkit-transition: opacity 0.5s linear, margin-top 0.5s linear; /* Safari */
  transition: opacity 0.5s linear, margin-top 0.5s linear;
}
.loading h1, .loading h2 {
  margin-top: 0px;
  opacity: 0;  
}
</style>
</head>

<link href="https://fonts.googleapis.com/css?family=Encode+Sans+Semi+Condensed:100,200,300,400" rel="stylesheet">
<body class="loading">
  <h1>@yield('code')</h1><h2>@yield('message')</h2>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <script type="text/javascript">$(function() {
  setTimeout(function(){
    $('body').removeClass('loading');
  }, 1000);
});
</script>
</body>
</html>