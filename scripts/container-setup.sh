#!/usr/bin/env bash
#
# Sets up an Apple `container` machine (macOS "container machine") running
# real Ubuntu, wires up SSH, writes the Ansible inventory, and optionally
# provisions WordPress + WooCommerce + NextStagePayments.
#
# Usage:
#   ./scripts/container-setup.sh            # VM + inventory only
#   ./scripts/container-setup.sh --provision   # ...and run the Ansible playbook
#
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
MACHINE="wp-dev"
IMAGE_TAG="local/ubuntu-machine:latest"
INVENTORY="$ROOT_DIR/ansible/inventory/local.yml"
DOCKERFILE="$ROOT_DIR/container-machine/Dockerfile.ubuntu"
REQUIREMENTS="$ROOT_DIR/ansible/requirements.yml"

log() { printf '\033[1;34m%s\033[0m\n' "$*"; }

# --- 1. Binary + service -----------------------------------------------------
if ! command -v container >/dev/null 2>&1; then
  log "Apple 'container' CLI missing - installing via Homebrew"
  brew install container
fi

if ! command -v ansible-playbook >/dev/null 2>&1; then
  log "Ansible missing - installing via Homebrew"
  brew install ansible
fi

if ! container system status >/dev/null 2>&1; then
  log "Starting the container service (installs the default kernel non-interactively)"
  container system start --enable-kernel-install
else
  log "Container service already running"
fi

# --- 2. Build the Ubuntu machine image ---------------------------------------
if ! container image list -q | grep -q "$IMAGE_TAG"; then
  log "Building Ubuntu 24.04 machine image ($IMAGE_TAG)"
  container build -f "$DOCKERFILE" -t "$IMAGE_TAG" "$ROOT_DIR"
else
  log "Machine image already present: $IMAGE_TAG"
fi

# --- 3. Create the container machine -----------------------------------------
# Note: `machine create` can report a spurious "Operation not supported by
# device" error while tearing down the FIRST boot (right after first-boot user
# setup succeeds). The machine usually still exists and boots fine afterward.
if ! container machine list -q | grep -qx "$MACHINE"; then
  log "Creating container machine '$MACHINE'"
  container machine create --cpus 2 --memory 4G --name "$MACHINE" "$IMAGE_TAG" || {
    log "machine create returned an error during first boot; checking whether it still exists"
    container machine list -q | grep -qx "$MACHINE" || {
      echo "ERROR: container machine '$MACHINE' was not created." >&2
      echo "Inspect with: container machine logs --boot $MACHINE" >&2
      exit 1
    }
  }
else
  log "Container machine '$MACHINE' already exists"
fi

container machine run -n "$MACHINE" --root -- systemctl enable --now ssh

# --- 4. SSH key + authorized keys --------------------------------------------
LDIR="${HOME}/.ssh"
KEY_FILE="${LDIR}/id_ed25519"
if [[ ! -f "$KEY_FILE" ]]; then
  log "Generating an SSH key for local VM access"
  mkdir -p "$LDIR" && chmod 700 "$LDIR"
  ssh-keygen -t ed25519 -N "" -f "$KEY_FILE" -C "container-machine-wp-dev" >/dev/null
fi

HOST_USER="$(id -un)"
HOST_GID="$(id -g)"
# sshd's effective AuthorizedKeysFile here is /etc/ssh/authorized_keys/%u, and
# sshd reads it under privilege separation AS the target user - so the dir must
# be traversable and the key file owned by the user, not root.
container machine run -n "$MACHINE" --root -- install -d -m 711 /etc/ssh/authorized_keys
container machine run -n "$MACHINE" --root -i -- tee "/etc/ssh/authorized_keys/$HOST_USER" < "$KEY_FILE.pub"
container machine run -n "$MACHINE" --root -- chown "$HOST_USER:$HOST_GID" "/etc/ssh/authorized_keys/$HOST_USER"
container machine run -n "$MACHINE" --root -- chmod 600 "/etc/ssh/authorized_keys/$HOST_USER"

# --- 5. Discover the machine's IP --------------------------------------------
IP="$(container machine list --format json | MACHINE="$MACHINE" python3 -c '
import json, os, sys, ipaddress

def find(d):
    if isinstance(d, dict):
        for k, v in d.items():
            if "ip" in k.lower() and isinstance(v, str):
                try:
                    if ipaddress.ip_address(v).version == 4:
                        return v
                except ValueError:
                    pass
            r = find(v)
            if r:
                return r
    elif isinstance(d, list):
        for v in d:
            r = find(v)
            if r:
                return r
    return None

machines = json.load(sys.stdin)
target = os.environ["MACHINE"]
for m in machines:
    if m.get("id") == target and m.get("status") == "running":
        print(find(m))
        break
')"
if [[ -z "$IP" ]]; then
  echo "ERROR: could not determine container machine IP address." >&2
  echo "Run: container machine list --format json" >&2
  exit 1
fi
log "Container machine '$MACHINE' is at http://$IP"

# --- 6. Verify SSH -----------------------------------------------------------
log "Verifying SSH to ${HOST_USER}@${IP}"
ssh -o StrictHostKeyChecking=accept-new -o ConnectTimeout=15 "${HOST_USER}@${IP}" true

# --- 7. Write the Ansible inventory ------------------------------------------
log "Writing Ansible inventory: $INVENTORY"
cat > "$INVENTORY" <<EOF
all:
  children:
    local:
      hosts:
        $MACHINE:
          ansible_host: $IP
          ansible_port: 22
          ansible_user: $HOST_USER
          ansible_become: true
          ansible_become_method: sudo
EOF

log "Installing Ansible collections"
ansible-galaxy collection install -r "$REQUIREMENTS"

# --- 8. Provision ------------------------------------------------------------
if [[ "${1:-}" == "--provision" ]]; then
  log "Provisioning WordPress + WooCommerce + NextStagePayments"
  ( cd "$ROOT_DIR/ansible" && ansible-playbook -i inventory/local.yml playbooks/site.yml )
  log "Done. Store is at http://$IP  (admin: /wp-admin)"
fi