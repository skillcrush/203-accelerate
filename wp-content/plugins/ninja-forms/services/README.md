# Ninja Forms Services

Requires PHP v5.6+.

## Definition of Terms

- Client: The WordPress installation where the Ninja Forms plugin is installed.
- Server: The site providing service functionality, ie [My.NinjaForms.com](https://my.ninjaforms.com).

## Registering Service Integrations

- Filter: `ninja_forms_services`

Note: This filter is implemented during an AJAX request, which is used to "live update" the current state of a service from the Ninja Forms Dashboard. The below properties can be set dynamically to correspond to the current "state" of the service.

Example
```php
add_filter( 'ninja_forms_services', function( $services ){
  $services[ 'my-service' ] => [
    'name' => esc_html__( 'My Service', 'textdomain' ),
    'slug' => 'my-service', // Duplicate of the array key.
    'description' => esc_html__( 'This is my service.', 'textdomain' ),
    'enabled' => true,
    'installPath' => 'my-plugin/my-plugin.php',
  ];
  return $services;
});
```

Properties:
- `name` string (required) The translatable, human-readable name of the service.
- `slug` string (required) The programatic reference for the registered service.
- `description` string (required) The short description to display on the services tab.
- `enabled` bool|null (required) Pass `null` to disable the toggle.
- `installPath` string (required) The expected plugin install path (inside of `/wp-content/plugins`).
- `learnMore` string (required) The content of the "Learn More" modal.

Additional properties for installed service plugins:
- `serviceLink` array (required) Properties for the external link to manage the service.
  - `text` string The content of the service link.
  - `href` string The URL of the service link.
  - `classes` string Add additional classes to the link, ie 'nf-button primary'.
  - `target` string Specify the anchor target.
- `connect_url` string Override the OAuth connection URL.
- `successMessage` string The content of the modal after the service is setup.
  - The success message can be triggered by passing the `?success` query string in the OAuth redirect with the `slug` of the service.

## OAuth Connection to My.NinjaForms.com

Ninja Forms services are provided via a secure OAuth connection to My.NinjaForms.com.

The `client` generates a local secret key which is passed to the `server` when connecting a service.

The `server` accepts the passed secret key, registers an new OAuth Client, and returns the OAuth Client ID.

Communication between the `server` and the `client` requires a `hash` of the combined OAuth Client ID and OAuth Client Secret.

Registered services have access to OAuth connection data via the `\NinjaForms\OAuth` class.

- `::is_connected()`
- `::get_client_id()`
- `::get_client_secret()`
- `::connect_url()`

### Customizing the OAuth Connect Flow

The OAuth flow can be customized the a specific service (for an optimization experience) by passing a `connect_url` (See above).

## Remote Plugin Installation

Service integrations are provided as additional plugins, which are installed remotely from the WordPress.org plugin directory.

This remote plugin installation uses a custom [Plugin_Installer_Skin](https://developer.wordpress.org/reference/classes/plugin_installer_skin/) in order to suppress any output feedback text - since this process happens asynchronously.

See [services/remote-installer-skin.php](/services/remote-installer-skin.php).

## Local Development

When developing with a local copy the Ninja Forms Server, specify the `NF_SERVER_URL`.

Example:
```php
define('NF_SERVER_URL', 'https://my.ninjaforms.test');
```
