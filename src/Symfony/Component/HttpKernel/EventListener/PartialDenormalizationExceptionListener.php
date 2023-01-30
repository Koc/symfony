<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class PartialDenormalizationExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if (!$throwable instanceof PartialDenormalizationException) {
            return;
        }

        $violations = new ConstraintViolationList();
        /** @var NotNormalizableValueException $exception */
        foreach ($throwable->getErrors() as $exception) {
            // fixme: how to translate this messages?
            $message = sprintf(
                'The type must be one of "%s" ("%s" given).',
                implode(', ', $exception->getExpectedTypes()),
                $exception->getCurrentType()
            );
            $parameters = [];
            if ($exception->canUseMessageForUser()) {
                $parameters['hint'] = $exception->getMessage();
            }
            $violations->add(new ConstraintViolation($message, '', $parameters, null, $exception->getPath(), null));
        }

        $event->setThrowable(new ValidationFailedException($throwable->getData(), $violations, $throwable));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -16],
        ];
    }
}
