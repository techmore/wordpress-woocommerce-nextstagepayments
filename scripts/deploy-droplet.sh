#!/usr/bin/env bash
#
# Provisions a DigitalOcean droplet (or any Ubuntu SSH host).
#
# Usage:
#   ./scripts/deploy-droplet.sh
#
# Prereqs: copy ansible/inventory/production.yml.example and
#          ansible/inventory/group_vars/production.yml.example and fill them in.
#
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
INVENTORY="$ROOT_DIR/ansible/inventory/production.yml"

if [[ ! -f "$INVENTORY" ]]; then
  echo "ERROR: $INVENTORY not found." >&2
  echo "Copy the .example files in ansible/inventory and ansible/inventory/group_vars" >&2
  echo "and fill in your droplet IP, user, domain, and secrets." >&2
  exit 1
fi

ansible-galaxy collection install -r "$ROOT_DIR/ansible/requirements.yml"
cd "$ROOT_DIR/ansible"
exec ansible-playbook -i inventory/production.yml playbooks/site.yml "$@"