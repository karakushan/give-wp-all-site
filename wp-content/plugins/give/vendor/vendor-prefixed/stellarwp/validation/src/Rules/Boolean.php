<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by impress-org on 21-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Give\Vendors\StellarWP\Validation\Rules;

use Closure;
use Give\Vendors\StellarWP\Validation\Contracts\Sanitizer;
use Give\Vendors\StellarWP\Validation\Contracts\ValidatesOnFrontEnd;
use Give\Vendors\StellarWP\Validation\Contracts\ValidationRule;

class Boolean implements ValidationRule, ValidatesOnFrontEnd, Sanitizer
{
    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public static function id(): string
    {
        return 'boolean';
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public static function fromString(string $options = null): ValidationRule
    {
        return new self();
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function __invoke($value, Closure $fail, string $key, array $values)
    {
        if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $fail(sprintf(__('%s must be an boolean', 'give'), '{field}'));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function serializeOption()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function sanitize($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
