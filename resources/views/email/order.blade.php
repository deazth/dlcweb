<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

    .info {
      text-align: justify;
    }
    .detail {
        color: #636b6f;
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
        background-image: linear-gradient(to right, yellow, magenta);
        border-radius: 10px;
    }

    </style>

  </head>
  <body>
    <div class="info">
      Your DLCM MyDevice request has been rejected.
    </div>
    <br />
    <div class="detail">
      Order No: {{ $data['orderno'] }} <br />
      Order Type: {{ $data['ordertype'] }} <br />
      Reason: {{ $data['reason'] }} <br />
    </div>
  </body>
</html>
