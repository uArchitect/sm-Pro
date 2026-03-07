<?php

return [
    'attributes' => [
        'restoran_adi'      => 'restaurant name',
        'restoran_adresi'   => 'restaurant address',
        'restoran_telefonu' => 'restaurant phone',
        'name'              => 'name',
        'email'             => 'email',
        'password'          => 'password',
    ],
    'required' => [
        'restoran_adi' => 'Restaurant name is required.',
        'restoran_adresi' => 'Restaurant address is required.',
        'restoran_telefonu' => 'Restaurant phone is required.',
        'name'         => 'Full name is required.',
        'email'        => 'Email is required.',
        'email.email'  => 'Please enter a valid email address.',
        'email.unique' => 'This email is already in use.',
        'password'     => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Passwords do not match.',
    ],
    'auth' => [
        'failed' => 'Email or password is incorrect.',
    ],
];
