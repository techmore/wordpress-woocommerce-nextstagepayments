PREFIX =
ANSIBLE_DIR := ansible

.PHONY: help setup provision provision-droplet ssh shell logs destroy

help:
	@echo "Targets:"
	@echo "  make setup                Create/start the local container machine + provision"
	@echo "  make provision            Re-run provisioning against the local container machine"
	@echo "  make provision-droplet    Provision a DigitalOcean droplet (needs ansible/inventory/production.yml)"
	@echo "  make ssh                  Open a shell inside the container machine"
	@echo "  make logs                 Tail container machine boot logs"
	@echo "  make stop                 Stop the container machine"
	@echo "  make destroy              Delete the container machine + its persistent storage"

setup:
	./scripts/container-setup.sh --provision

provision:
	cd $(ANSIBLE_DIR) && ansible-playbook -i inventory/local.yml playbooks/site.yml

provision-droplet:
	./scripts/deploy-droplet.sh

ssh:
	container machine run -n wp-dev

logs:
	container machine logs -n 200 wp-dev

stop:
	container machine stop wp-dev

destroy:
	container machine delete wp-dev