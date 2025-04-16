<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();
        $this->form->fill([
            'email' => '',
            'password' => '',
            'remember' => false,
        ]);
    }

    public function getHeading(): string
    {
        return 'G6BOUTIQ';
    }

    public function getSubheading(): string
    {
        return 'Nos Articles les meilleurs';
    }
} 