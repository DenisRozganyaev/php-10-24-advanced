# Run Composer inside docker container (alias)
c *args:
    just composer {{args}}

# Run Composer inside docker container
composer *args:
    docker compose exec app composer {{args}}

logs *args:
    docker compose logs {{args}}

# SSH into a container
ssh container_name:
    docker exec -it {{container_name}} bash

# Up docker
up *args:
    docker compose up {{args}}
