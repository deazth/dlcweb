<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

    .info {
      align: justify;
    }
    .detail > a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }

    </style>

  </head>
  <body>
    <div class="info">
      Your device return request has been approved. Please proceed to send the device to the nearest collection point.
    </div>
    <div class="detail">
      Order No: {{ $data['orderno'] }}
    </div>
  </body>
</html>
