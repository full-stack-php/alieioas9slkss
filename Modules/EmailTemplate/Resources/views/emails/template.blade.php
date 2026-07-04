<!DOCTYPE html>
<html lang="{{ locale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; color: #444;">
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 24px;">
            @if(!empty($header))
                <div style="margin-bottom: 20px;">
                    {!! $header !!}
                </div>
            @endif

            <div>
                {!! $body !!}
            </div>

            @if(!empty($footer))
                <div style="margin-top: 24px; color: #777; font-size: 13px;">
                    {!! $footer !!}
                </div>
            @endif
        </td>
    </tr>
</table>
</body>
</html>
