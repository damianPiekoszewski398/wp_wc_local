up:
	docker-compose up -d

down:
	docker-compose down

logs:
	docker-compose logs -f

shell:
	docker-compose exec wordpress bash

reset:
	docker-compose down -v


