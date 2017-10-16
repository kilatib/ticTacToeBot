<?php

namespace AppBundle;

/**
 * Class AppBundleException
 *      this Exception was create to provide one more layer between Symfony Exception
 *          and exception happen inside this bundle
 *
 * @package AppBundle
 */
class AppBundleException extends \Exception
{
    const NO_CONTENT = 'No data';
}
