<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code for Changing Vending PIN</title>
    @include('emails.partials.style')
    <style>
        .otp {
            font-size: 32px;
            font-weight: bold;
            color: #2d3748;
            text-align: center;
            margin: 30px 0;
        }
    </style>
</head>
<body>

<div class="container">
    @include('emails.partials.header')

    <div class="content">
        <div class="greeting">Hello {{$to['name']}},</div>

        <div style="margin: 20px 0px;">
            {{ $data['body'] }}
        </div>
        <div style="margin: 50px 0px;">
            <div class="otp">
                {{ $data['otp'] }}
            </div>
        </div>
        <div style="margin: 50px 0px;">
            <div style="margin: 40px 0; background-color: #f3f4f6; padding: 20px; border-radius: 8px;">
                <ol style="margin: 0; padding-left: 20px;">
                    <li><strong>OTP Code:</strong> {{ $data['otp'] }}</li>
                    <li><strong>Will Expire:</strong> {{ $data['validity'] }}</li>
                    <li>Enter the OTP code into the Vending Admin to verify your identity.</li>
                    <li>Once the OTP is validated, enter your new PIN.</li>
                    <li>Click the submit button to complete the PIN reset process.</li>
                </ol>
            </div>
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