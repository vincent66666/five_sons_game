.PHONY: git-pull
git-pull:
	git pull

.PHONY: build
build:
	docker build -t hyperf_server:hyperf_cli .

.PHONY: git-build
git-build:
	git pull
	docker build -t hyperf_server:hyperf_cli .

.PHONY: start
start:
	docker run -p 9602:9602 --name hyperf_server -dit hyperf_server:hyperf_cli

.PHONY: stop
stop:
	docker stop hyperf_server

.PHONY: delete
delete:
	docker rm -f hyperf_server

.PHONY: deploy
deploy:  build stop delete start

.PHONY: git-deploy
git-deploy:  git-build stop delete start


.PHONY: git-pull-o
git-pull-o:
	git pull
	composer dump-autoload -o

.PHONY: supervisor-start
supervisor-start:
	/www/server/panel/pyenv/bin/supervisorctl  start hyperf_server:hyperf_server_00

.PHONY: supervisor-restart
supervisor-restart:
	/www/server/panel/pyenv/bin/supervisorctl  restart hyperf_server:hyperf_server_00

.PHONY: supervisor-stop
supervisor-stop:
	/www/server/panel/pyenv/bin/supervisorctl stop hyperf_server:hyperf_server_00

.PHONY: supervisor-status
supervisor-status:
	/www/server/panel/pyenv/bin/supervisorctl status

.PHONY: git-supervisor-deploy
git-supervisor-deploy:  git-pull-o supervisor-restart




