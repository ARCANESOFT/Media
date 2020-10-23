<?php namespace Arcanesoft\Media\Rules;

use Arcanesoft\Media\MediaManager;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class     MediaItemExistsRule
 *
 * @package  Arcanesoft\Media\Rules
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaItemExistsRule implements Rule
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Media\MediaManager */
    protected $manager;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    public function __construct(MediaManager $manager)
    {
        $this->manager = $manager;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->manager->exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'The attribute :attribute does not exists';
    }
}
