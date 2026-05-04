<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case EMPLOYE = 'ROLE_EMPLOYE';
    case CLIENT = 'ROLE_CLIENT';
}