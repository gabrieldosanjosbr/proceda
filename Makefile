.PHONY: build up restart rebuild down logs

build:
	docker compose --profile build build runtime
	docker compose build writer

up:
	docker compose up -d

restart:
	docker compose restart writer

rebuild:
	docker compose --profile build build --no-cache runtime
	docker compose build --no-cache writer
	docker compose up -d --force-recreate

logs:
	docker compose logs -f writer

down:
	docker compose down