FROM mysql:latest

COPY database/database_dump.sql /docker-entrypoint-initdb.d/