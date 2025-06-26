<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code for Changing Vending PIN</title>
    @include('emails.partials.style')
</head>
<body>

<div class="container">
    @include('emails.partials.header')

    <div class="content">
        <div class="greeting">Hello {{$to['name']}},</div>

        <div style="margin: 20px 0px;">
            {{ $data['body'] }}
        </div>

        <div style="margin: 20px 0px;">
            If you did not authorize this action, please contact our <strong>Support</strong> immediately.
            <br><br>
            Thank you very much.
            <br><br>
            Regards,<br>
            {{ config('app.name') }}
        </div>
        <br/>
    </div>

    @include('emails.partials.footer')
</div>

</body>
</html>