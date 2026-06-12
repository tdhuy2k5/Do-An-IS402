# =========================
# 0. BASE STAGE
# =========================
FROM node:20-alpine AS base

WORKDIR /app

COPY package*.json ./
RUN npm ci


# =========================
# 1. CI / TEST STAGE (QUALITY GATE)
# =========================
FROM base AS tester

WORKDIR /app

COPY . .

# 🔥 CI QUALITY GATE (THIS IS WHAT YOU WANTED)
RUN npm run lint
RUN npm run test

# =========================
# 2. BUILD STAGE
# =========================
FROM base AS build

WORKDIR /app

COPY . .

ARG VITE_BACKEND_API_URL
ARG VITE_GOOGLE_CLIENT_ID
ARG VITE_GOOGLE_REDIRECT_URI

RUN if [ -z "$VITE_BACKEND_API_URL" ] || \
    [ -z "$VITE_GOOGLE_CLIENT_ID" ] || \
    [ -z "$VITE_GOOGLE_REDIRECT_URI" ]; then \
    echo "❌ Missing required VITE environment variables" && exit 1; \
    fi

ENV VITE_BACKEND_API_URL=$VITE_BACKEND_API_URL
ENV VITE_GOOGLE_CLIENT_ID=$VITE_GOOGLE_CLIENT_ID
ENV VITE_GOOGLE_REDIRECT_URI=$VITE_GOOGLE_REDIRECT_URI

RUN npm run build


# =========================
# 3. PRODUCTION STAGE
# =========================
FROM pierrezemb/gostatic AS production

WORKDIR /srv/http

COPY --from=build /app/dist /srv/http

EXPOSE 5173

CMD ["-fallback", "/index.html", "-port", "5173"]