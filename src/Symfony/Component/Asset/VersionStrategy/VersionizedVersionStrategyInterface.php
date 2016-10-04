<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Asset\VersionStrategy;

/**
 * Asset version strategy interface.
 *
 * @author Konstantin Myajshin <koc-dp@yandex.ru>
 */
interface VersionizedVersionStrategyInterface extends VersionStrategyInterface
{
    /**
     * Applies version to the supplied path.
     *
     * @param string $path    A path
     * @param string $version The asset version
     *
     * @return string The versionized path
     */
    public function applyVersion($path, $version = null);
}
