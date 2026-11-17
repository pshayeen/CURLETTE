<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Shared password strength rules for registration, admin user form and change-password.
 */
final class PasswordRules
{
    public const HELP = 'At least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.';

    /**
     * @return list<\Symfony\Component\Validator\Constraint>
     */
    public static function constraints(bool $required = true): array
    {
        $rules = [
            new Length(
                min: 8,
                minMessage: 'Password must be at least {{ limit }} characters long.',
                max: 4096,
            ),
            new Regex(pattern: '/[a-z]/', message: 'Password must contain at least one lowercase letter.'),
            new Regex(pattern: '/[A-Z]/', message: 'Password must contain at least one uppercase letter.'),
            new Regex(pattern: '/\d/', message: 'Password must contain at least one number.'),
            new Regex(pattern: '/[^A-Za-z0-9]/', message: 'Password must contain at least one special character (e.g. ! @ # $ %).'),
        ];

        if ($required) {
            array_unshift($rules, new NotBlank(message: 'Please enter a password.'));
        }

        return $rules;
    }
}
