# Lapis

## Description

This project is the foundation for creating a web API application. It includes a backend written in PHP using packages
from the [Nette framework](https://nette.org/) and a frontend implemented in [Svelte](https://svelte.dev/)
and [SvelteKit](https://kit.svelte.dev/).

## Structure of project

```
./
├── backend/
│    ├── app/            # server's application code
│    │    ├── Core/      # routing, model, security...
│    │    └── Endpoint/  # domains of handlers for endpoints
│    └── src/            # base server's code
│
├── frontend/
│    ├── app/            # client's application code
│    │    └── routes/    # pages of application
│    └── src/            # base client's code
│
├── static/              # static assets
├── www/                 # application's domain root
└── docker/              # settings for Docker's containers
```

## Developing

For local development, [Docker](https://www.docker.com/) containers are prepared and composed
using [Docker Compose](https://docs.docker.com/compose/).

```bash
# building
docker compose build

# running
docker compose up -d
```

## Building

WIP