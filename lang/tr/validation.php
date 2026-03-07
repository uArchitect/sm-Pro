<?php

return [
    'attributes' => [
        'firma_adi'         => 'firma adı',
        'restoran_adi'      => 'restoran adı',
        'restoran_adresi'   => 'restoran adresi',
        'restoran_telefonu' => 'restoran telefonu',
        'name'              => 'ad soyad',
        'email'             => 'e-posta',
        'password'          => 'şifre',
    ],
    'required' => [
        'firma_adi'    => 'Firma adı zorunludur.',
        'restoran_adi' => 'Restoran adı zorunludur.',
        'restoran_adresi' => 'Restoran adresi zorunludur.',
        'restoran_telefonu' => 'Restoran telefonu zorunludur.',
        'name'         => 'Ad soyad zorunludur.',
        'email'        => 'E-posta zorunludur.',
        'email.email'  => 'Geçerli bir e-posta adresi girin.',
        'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
        'password'     => 'Şifre zorunludur.',
        'password.min' => 'Şifre en az 8 karakter olmalıdır.',
        'password.confirmed' => 'Şifreler eşleşmiyor.',
    ],
    'auth' => [
        'failed' => 'E-posta veya şifre hatalı.',
    ],
];
