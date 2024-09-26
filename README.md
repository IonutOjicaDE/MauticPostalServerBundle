# MauticPostalServerBundle
This is a fork of [Pacerino/MauticPostalServerBundle](https://github.com/Pacerino/MauticPostalServerBundle) which is a fork of [LucasCCS/postal_server_api_transport_for_mautic](https://github.com/LucasCCS/postal_server_api_transport_for_mautic).

This plugin makes it possible to use Postal as API sender in Mautic and to receive bounces and complaints via webhook.

### Tested with
- Postal: 3.3.3
- Mautic: 4.4.12


# How to install?

1. Go to the plugins folder and clone this repository `git clone https://github.com/IonutOjicaDE/MauticPostalServerBundle.git`
2. On your Postal installation, execute `postal default-dkim-record`
3. Copy the key/part after `p=` without the semicolon
4. Edit the `Config/config.php` and put the Key into the Line 45 `'mailer_postal_webhook_signing_key' => 'KEY_GOES_HERE',` save and exit
5. Clear the cache `rm -rf var/cache/*`
6. Reload the plugins via console `sudo -u www-data php var/www/mautic/bin/console mautic:plugins:reload` or go to `https://mymautic.org/s/plugins` and click _Install/Upgrade Plugins_
7. In your Mautic, switch the Sender to `Postal` and fill out your credentials
8. In Postal add the webhook `https://mymautic.org/mailer/postal/callback` and select the `MessageDeliveryFailed` and `MessageBounced` events

## Contributors

- [LucasCCS](https://github.com/LucasCCS) - Original Idea
- [Pacerino](https://github.com/Pacerino) - Implementing, Bugfix and Testing
- [Fabio from Canada](https://github.com/fanpero87) - Testing
