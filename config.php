<?php

/*
 * alexa-mint-skill config
 *
 * The Mint integration with mintapi will require a Mint account with
 * configured accounts and browser session cookies (ius_session and
 * thx_guid). These can be obtained with your web browser.
 *
 * See https://github.com/mrooney/mintapi
 *
 */

$configArray = array(
        'username'      => 'user@test.com',
        'password'      => 'password',
        'ius_session'   => '',
        'thx_guid'      => '',
        'mintApi'       => '/opt/python-2.7.2/bin/mintapi', // Location of mintapi script
);

?>
