<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Redirecting to payment gateway') }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f7f7f7; color: #222; }
        .card { max-width: 720px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 6px 24px rgba(0,0,0,.08); }
        button { background: #3f51b5; color: #fff; border: 0; padding: 12px 18px; border-radius: 4px; cursor: pointer; }
        .meta { font-size: 0.95rem; color: #555; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ __('Preparing your payment request') }}</h2>
    <p class="meta">{{ __('Challan') }}: {{ $subscription->challan }}</p>
    <p class="meta">{{ __('Amount') }}: {{ $subscription->amount }}</p>
    <p>{{ __('If you are not redirected automatically, use the button below to continue to the payment gateway.') }}</p>

    <form id="gateway-form" method="POST" action="{{ $paymentUri }}">
        @foreach($payload as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
        @endforeach
        <button type="submit">{{ __('Continue to payment') }}</button>
    </form>
</div>
<script>
    document.getElementById('gateway-form').submit();
</script>
</body>
</html>