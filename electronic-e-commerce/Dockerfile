# =========================
# 0. BASE STAGE
# =========================
FROM node:20-alpine AS base

WORKDIR /app

COPY package*.json ./
RUN npm ci

# =========================
# 1. TEST STAGE
# =========================
FROM base AS tester

WORKDIR /app
COPY . .

RUN npm run lint
RUN npm run test

# =========================
# 2. BUILD STAGE (HARD CODED ENV)
# =========================
FROM base AS build

WORKDIR /app
COPY . .

# 🔥 HARD-CODED VALUES (NO Kubernetes needed)
ENV VITE_BACKEND_API_URL="http://fe.135.171.243.189.nip.io/api"
ENV VITE_GOOGLE_CLIENT_ID="518525301067-6vci81s4s8eo4klif1rmj18rl8o216po.apps.googleusercontent.com"
ENV VITE_GOOGLE_REDIRECT_URI="/auth/callback"

RUN npm run build

# =========================
# 3. PRODUCTION
# =========================
FROM pierrezemb/gostatic AS production

WORKDIR /srv/http

COPY --from=build /app/dist /srv/http

EXPOSE 5173

CMD ["-fallback", "/index.html", "-port", "5173"]