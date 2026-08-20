# WordPress + WooCommerce + NextStagePayments provisioning

Ansible-based provisioning that installs WordPress, WooCommerce, and a
NextStagePayments payment gateway on Ubuntu machines. It's designed for
[DigitalOcean droplets](https://www.digitalocean.com/products/droplets/) and can
be developed/tested locally on a Mac using Apple's [container tool](https://github.com/apple/container)
**container machines** (real Ubuntu VMs — no Docker).

## Layout

```
ansible/                 Ansible playbooks, inventories, roles
  playbooks/site.yml     The provisioning playbook
  roles/
    common/    base packages, timezone, optional firewall (UFW)
    php/       PHP-FPM + extensions for WordPress/WooCommerce
    mariadb/   MariaDB server + database/user for WordPress
    nginx/     Nginx vhost, optional Let's Encrypt via certbot
    wordpress/ wp-cli, WordPress core, WooCommerce, theme, extra plugins
    nextstage/ installs + activates the NextStagePayments gateway plugin
  inventory/   local.yml (generated) / production.yml.example / group_vars (all, local, production)
container-machine/       Dockerfile for the local Ubuntu 24.04 machine image
plugins/nextstagepayments/   Stub WooCommerce payment gateway plugin
scripts/                 container-setup.sh, deploy-droplet.sh
```

## Requirements

- macOS 26+ on Apple silicon (for local container machines)
- [Homebrew](https://brew.sh)
- `make`
- A DigitalOcean droplet (Ubuntu) for real deployments

## Local development (container machine)

Everything local runs in an Apple `container` machine — a persistent Ubuntu 24.04
VM with systemd, accessed over plain SSH by Ansible (identical to how a droplet
is accessed). No Docker required.

```bash
brew install container && container system start   # one time only
make setup                                          # VM + inventory + full provisioning
```

`make setup` will:

1. build the Ubuntu machine image from `container-machine/Dockerfile.ubuntu`
2. create the `wp-dev` container machine
3. enable sshd, install your local SSH public key
4. discover the machine's IP and write `ansible/inventory/local.yml`
5. run `ansible-playbook ansible/playbooks/site.yml`

When it finishes, open the printed `http://<machine-ip>` in your browser
(`/wp-admin` for the admin UI, default user `admin` / `admin`).

Other targets: `make provision`, `make ssh`, `make logs`, `make stop`, `make destroy`.

## DigitalOcean droplet

1. Copy the example files and fill in your details:

   ```bash
   cp ansible/inventory/production.yml.example ansible/inventory/production.yml
   cp ansible/inventory/group_vars/production.yml.example ansible/inventory/group_vars/production.yml
   ```

2. Make sure your droplet has your Mac's public key in `~/.ssh/authorized_keys`.

3. Run:

   ```bash
   make provision-droplet
   ```

In `ansible/inventory/group_vars/production.yml` set your domain, admin credentials,
database password, and `enable_ssl: true`. With SSL enabled the playbook
issues a Let's Encrypt certificate via certbot (your domain's DNS must point at
the droplet first). Protect secrets with `ansible-vault encrypt ansible/inventory/group_vars/production.yml`.

## NextStagePayments

`make setup` / the playbook installs and activates the stub gateway under
`plugins/nextstagepayments/`. To wire it up for real:

1. Get merchant credentials and the API endpoint from Next Stage Payments
   (https://nextstagepayments.com/developers/).
2. Replace the placeholder request in
   `plugins/nextstagepayments/includes/class-nsp-api.php`.
3. Configure the gateway in WP admin under
   **WooCommerce > Settings > Payments > NextStagePayments** (it ships in "test
   mode", which approves orders without contacting the gateway).

## Extending with other requirements

Any extra WordPress plugins can be added as slugs in the `wp_extra_plugins` list
in `ansible/inventory/group_vars/all.yml`. For non-plugin requirements, add a role in
`ansible/roles/` and include it in `ansible/playbooks/site.yml`.

## Notes

- Default credentials are for local development only — change them for anything
  real.
- The local container machine has an isolated network; reach the store at the
  machine's own IP, not `localhost`.