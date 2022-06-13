<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Verification </title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/cover/">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
/*
 * Globals
 */


/* Custom default button */
.btn-secondary,
.btn-secondary:hover,
.btn-secondary:focus {
  color: #333;
  text-shadow: none; /* Prevent inheritance from `body` */
}


/*
 * Base structure
 */

body {
  text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
  box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
}

.cover-container {
  max-width: 42em;
}


/*
 * Header
 */

.nav-masthead .nav-link {
  padding: .25rem 0;
  font-weight: 700;
  color: rgba(255, 255, 255, .5);
  background-color: transparent;
  border-bottom: .25rem solid transparent;
}

.nav-masthead .nav-link:hover,
.nav-masthead .nav-link:focus {
  border-bottom-color: rgba(255, 255, 255, .25);
}

.nav-masthead .nav-link + .nav-link {
  margin-left: 1rem;
}

.nav-masthead .active {
  color: #fff;
  border-bottom-color: #fff;
}


    </style>

  </head>
  <body class="d-flex h-100 text-center text-white" style="background: #04060c;">

<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
  <main class="px-3">
  <div class="container mt-4">
      <form name="verify-phone-form" id="verify-phone-form" method="post" action="{{url('verification-form-phone')}}">
       @csrf
        <div class="form-group mb-2">
          <input type="text" id="phonenumber" name="phonenumber" placeholder="Enter verification code.." class="form-control" required="">
        </div>
        <div class="d-grid gap-2">        <button type="submit" class="btn btn-primary mt-2 btn-lg">Submit</button>
  </div>
      </form>
    <div class="mb-5">
      <form name="verify-phone-sendcode-form" id="verify-phone-sendcode-form" method="post" action="{{url('verify-phone-sendcode')}}">
       @csrf
    <div class="d-grid gap-1">
        <button type="submit" class="btn btn-outline-primary mt-2 btn-small">Send SMS Verification Code</button>
    </div>
      </form>
  </div>
</div>
  </main>
	</div>
  </body>
</html>
