<x-mail::message>
# Reset Password

Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.

<x-mail::button :url="$actionUrl">
Reset Password
</x-mail::button>

Link ini akan kadaluarsa dalam {{ config('auth.passwords.users.expire') }} menit.

Jika Anda tidak meminta reset password, abaikan email ini.

Terima kasih,<br>
{{ config('app.name') }}

<x-slot:subcopy>
Jika Anda mengalami masalah dengan tombol "Reset Password", copy dan paste URL berikut ke browser Anda: <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
</x-mail::message>