<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('institution_acronym', 'eBoard') }}</title>
</head>
<body style="background-color: #f4f4f4; padding: 20px;">

<table class="table table-borderless mx-auto" style="max-width: 600px; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <tr>
        <td style="padding: 0;">
            @include('emails.partials._header')
        </td>
    </tr>
    <tr>
        <td style="padding: 30px; text-align: center; font-size: 20px; font-weight: bold; color: {{ setting('primary_color', '#1a1a1a') }};">
            {{ $subject }}
        </td>
    </tr>
    <tr>
        <td style="padding: 20px; font-size: 16px; line-height: 1.6;">
            {!! $body !!}
        </td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 30px;">
            <a href="{{ setting('website_url', url('/')) }}" style="background-color: {{ setting('primary_color', '#1a1a1a') }}; border: none; padding: 10px 20px; border-radius: 5px; color: white; text-decoration: none;">
                Visit Our Website
            </a>
        </td>
    </tr>
    <tr>
        <td style="padding: 0;">
            @include('emails.partials._footer')
        </td>
    </tr>
</table>

</body>
</html>
