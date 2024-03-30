# Simple Order Service

**Author:** James Wedam Anewenah

**Stack:** [Symfony](https://symfony.com/) / [GraphQL](https://github.com/Wedrix/watchtower-symfony-bundle)

**Templates:** [Symfony Docker](https://github.com/dunglas/symfony-docker)

## Project Setup

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)

2. Run `docker compose build --no-cache` to build fresh images

3. Run `SYMFONY_VERSION=6.1.* docker compose up --pull always -d --wait` to start the project

4. Run `docker exec -d replace-with-container-name bin/console doctrine:schema:create` to migrate the database. Remember to replace the actual container name.

5. Open `https://localhost` in Chrome and [accept the auto-generated TLS certificate](https://youtu.be/7J3vSN3pCjI?si=kOxw6vNHPmyptSto).

6. Interact with the API using an appropriate browser-based GraphQL client like [GraphiQL](https://chromewebstore.google.com/detail/graphiql-extension/jhbedfdjpmemmbghfecnaeeiokonjclb).

7. The API endpoint is `https://localhost/graphql.json`

8. Authentication is done using tokens. The response after signing up/signing in contains an `X-Auth-Token` header with the authentication token. You can view the response header by [accessing chrome's dev-tools](https://www.youtube.com/watch?v=hqQR1O2H_ck). Pass the token header with subsequent requests to authenticate them. The image below demonstrates how to do this with the aforementioned [GraphiQL](https://chromewebstore.google.com/detail/graphiql-extension/jhbedfdjpmemmbghfecnaeeiokonjclb) client.

9. Run `docker compose down --remove-orphans` to stop the Docker containers.

10. It is recommended to [install Docker Desktop](https://www.docker.com/products/docker-desktop/) for easy viewing/management of containers.

![Demo Image](https://github.com/wedrix/watchtower_order_service/blob/main/demo.png?raw=true)

## API Description

### Scalars

- Limit - Integer from **1** to **100**
- Page - Integer greater than or equal to **1**
- EmailAddress - Email address
- Name - String consisting of only chars or spaces
- Password - String of length greater than or equal to **8**
- Price - Decimal String with two-point precision in range **0.00 - 1000000000.00**
- ProductName - String consisting of only chars, digits, or spaces
- Role - Either **ROLE_USER** or **ROLE_ADMIN**
- Stock - Integer greater than or equal to **0** but less than **1000000**
