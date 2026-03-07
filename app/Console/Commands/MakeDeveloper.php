<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MakeDeveloper extends Command
{
    protected $signature   = 'make:developer {--email=dev@siparismus.com} {--name=Developer} {--password=}';
    protected $description = 'Platform developer (süper yönetici) hesabı oluştur veya güncelle';

    public function handle(): int
    {
        $email    = $this->option('email');
        $name     = $this->option('name');
        $password = $this->option('password') ?: $this->secret('Şifre (boş bırakırsan varsayılan: dev123456)') ?: 'dev123456';

        $exists = DB::table('users')->where('email', $email)->first();

        if ($exists) {
            DB::table('users')->where('email', $email)->update([
                'name'       => $name,
                'role'       => 'developer',
                'password'   => Hash::make($password),
                'updated_at' => now(),
            ]);
            $this->info("✓ Developer hesabı güncellendi: {$email}");
        } else {
            DB::table('users')->insert([
                'tenant_id'  => null,
                'name'       => $name,
                'email'      => $email,
                'password'   => Hash::make($password),
                'role'       => 'developer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->info("✓ Yeni developer hesabı oluşturuldu: {$email}");
        }

        $this->table(
            ['Alan', 'Değer'],
            [['E-posta', $email], ['Şifre', $password], ['Rol', 'developer']]
        );

        return self::SUCCESS;
    }
}
