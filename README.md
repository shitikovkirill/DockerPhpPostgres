# Php Docker container

### Example
'''
version: "3"
services:

  php:
    image: kennykwey/dockerphppostgres:devv1
    container_name: medtest_dev
    command: php -S 0.0.0.0:8000 -t public
    ports:
      - "8000:8000"
    depends_on:
      - "db"
      - "redis"
    env_file:
      .env

  db:
    image: "postgres:11"
    container_name: medtest_db
    volumes:
      - pgdata:/var/lib/postgresql/data

  redis:
    image: "redis:alpine"
    container_name: medtest_redis

  blackfire:
    image: blackfire/blackfire
    env_file:
      .env

volumes:
  pgdata:
'''
