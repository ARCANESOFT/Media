<?php namespace Arcanesoft\Media\Tests\Unit\Helpers;

use Arcanesoft\Media\Helpers\ExcludePattern;
use Arcanesoft\Media\Tests\TestCase;

/**
 * Class     ExcludePatternTest
 *
 * @package  Arcanesoft\Media\Tests\Unit\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ExcludePatternTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /**
     * @test
     *
     * @dataProvider getPatternsForDirectoriesToExclude
     *
     * @param  array  $patterns
     * @param  array  $expected
     */
    public function it_can_get_exclude_patterns_for_directories(array $patterns, $expected)
    {
        $this->assertSame($expected, ExcludePattern::directories($patterns));
    }

    public function getPatternsForDirectoriesToExclude()
    {
        return [
            [
                ['folder-1'], ['folder-1', 'folder-1/*'],
            ],[
                ['folder-1/*'], ['folder-1', 'folder-1/*'],
            ],[
                ['folder-1', 'folder-1/*'], ['folder-1', 'folder-1/*'],
            ],[
                ['folder-1/*', 'folder-1'], ['folder-1', 'folder-1/*'],
            ],[
                ['folder-1', 'folder-2'], ['folder-1', 'folder-1/*', 'folder-2', 'folder-2/*'],
            ],[
                ['folder-2', 'folder-1'], ['folder-1', 'folder-1/*', 'folder-2', 'folder-2/*'],
            ],
        ];
    }
}
