@component('mail::message')
# Reset Password

You have indicated that you forgot your Pompong password.

If you did not make this request, please ignore this email.

The request will expire and become useless in 2 hours.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
