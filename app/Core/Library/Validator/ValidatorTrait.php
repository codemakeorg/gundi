<?php
namespace Core\Library\Validator;

use Watson\Validating\ValidatingTrait;

trait ValidatorTrait
{
    use ValidatingTrait;

    /**
     * Boot the trait. Adds an observer class for validating.
     *
     * @return void
     */
    public static function bootValidatingTrait()
    {
        static::observe(new ValidatingObserver);
    }

    /**
     * Get the Validator instance
     *
     * @return \Illuminate\Validation\Factory
     */
    public function getValidator()
    {
        return $this->validator ?: Gundi()->validator;
    }
}