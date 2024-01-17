FROM mysql:latest

COPY ./structure.sql /docker-entrypoint-initdb.d/