# =========================
# 1. BUILD STAGE
# =========================
FROM node:20-alpine AS build

WORKDIR /app

# Copy dependencies first (better caching)
COPY package*.json ./
RUN npm ci

# Copy source code
COPY . .

ARG VITE_BACKEND_API_URL
ARG VITE_GOOGLE_CLIENT_ID
ARG VITE_GOOGLE_REDIRECT_URI

ENV VITE_BACKEND_API_URL=$VITE_BACKEND_API_URL
ENV VITE_GOOGLE_CLIENT_ID=$VITE_GOOGLE_CLIENT_ID
ENV VITE_GOOGLE_REDIRECT_URI=$VITE_GOOGLE_REDIRECT_URI
RUN npm run build


# =========================
# 2. PRODUCTION STAGE
# =========================
FROM pierrezemb/gostatic AS production

WORKDIR /srv/http

# Copy build output
COPY --from=build /app/dist /srv/http


# NOTE:
# .env will NOT exist here (this is correct behavior)

EXPOSE 80

CMD ["-fallback", "/index.html", "-port", "5173"]