<?php

/**
 * Acquia/CommerceManager/registration.php
 *
 * Module Registration for the Acquia Commerce Manager integration configuration.
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Acquia_CommerceManager',
    __DIR__
);
