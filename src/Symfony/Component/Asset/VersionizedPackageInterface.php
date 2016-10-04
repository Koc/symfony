<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Asset;

/**
 * Asset package interface.
 *
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
interface VersionizedPackageInterface extends PackageInterface
{
    /**
     * Returns an absolute or root-relative public path.
     *
     * @param string $path    A path
     * @param string $version The asset version
     *
     * @return string The public path
     */
    public function getUrl($path, $version = null);
}
